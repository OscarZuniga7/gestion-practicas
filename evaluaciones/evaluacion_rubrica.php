<?php
require_once('../includes/db.php');
include('../includes/header.php');

/* ---------------------------
   Parámetros de entrada
---------------------------- */
$evaluacion_id       = isset($_GET['evaluacion_id']) ? (int)$_GET['evaluacion_id'] : 0;
$rubrica_id          = isset($_GET['rubrica_id'])     ? (int)$_GET['rubrica_id']     : 0;

$selected_estudiante = isset($_GET['estudiante_id'])  ? (int)$_GET['estudiante_id']  : null;
$selected_hito       = isset($_GET['hito_id'])        ? (int)$_GET['hito_id']        : null;
$selected_supervisor = isset($_GET['supervisor_id'])  ? (int)$_GET['supervisor_id']  : null;
$tipo_param          = $_GET['tipo'] ?? null; // 'interno' | 'externo' | 'común'

$modo_edicion            = false;
$niveles_actuales        = [];
$observaciones_actuales  = '';

/* ---------------------------
   Modo edición (carga eval)
---------------------------- */
if ($evaluacion_id > 0) {
    $modo_edicion = true;

    // Recuperar rubrica y contexto desde la evaluación existente
    $stmt = $pdo->prepare("
        SELECT c.rubrica_id, ev.estudiante_id, ev.hito_id, ev.supervisor, ev.observaciones
        FROM evaluaciones ev
        JOIN evaluaciones_criterios ec ON ec.evaluacion_id = ev.id
        JOIN criterios c               ON c.id = ec.criterio_id
        WHERE ev.id = ? LIMIT 1
    ");
    $stmt->execute([$evaluacion_id]);
    if ($row = $stmt->fetch()) {
        $rubrica_id             = (int)$row['rubrica_id'];
        $selected_estudiante    = (int)$row['estudiante_id'];
        $selected_hito          = (int)$row['hito_id'];
        $selected_supervisor    = is_numeric($row['supervisor']) ? (int)$row['supervisor'] : null; // por si supervisor guarda texto
        $observaciones_actuales = (string)$row['observaciones'];

        // Cargar niveles ya elegidos
        $stmt = $pdo->prepare("SELECT criterio_id, nivel_logro_id FROM evaluaciones_criterios WHERE evaluacion_id = ?");
        $stmt->execute([$evaluacion_id]);
        $niveles_actuales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

/* ------------------------------------------------
   Si no llega rubrica_id: resolver con hito + tipo
------------------------------------------------- */
if ($rubrica_id <= 0 && $selected_hito) {
    // si no vino 'tipo' pero sí supervisor_id, intentar inferirlo
    if (!$tipo_param && $selected_supervisor) {
        $q = $pdo->prepare("SELECT tipo FROM supervisores WHERE id = ?");
        $q->execute([$selected_supervisor]);
        $tipo_param = $q->fetchColumn() ?: null;
    }

    // buscar rúbrica que coincida con tipo o 'común'
    $sqlRub = "
      SELECT id
      FROM rubricas
      WHERE hito_id = :hito
        AND (:tipo IS NULL OR tipo_practica = :tipo OR tipo_practica = 'común')
      ORDER BY 
        CASE 
          WHEN tipo_practica = :tipo THEN 0
          WHEN tipo_practica = 'común' THEN 1
          ELSE 2
        END, id ASC
      LIMIT 1
    ";
    $st = $pdo->prepare($sqlRub);
    $st->execute([':hito' => $selected_hito, ':tipo' => $tipo_param]);
    $rubrica_id = (int)$st->fetchColumn();

    // último fallback: cualquier rúbrica del hito
    if ($rubrica_id <= 0) {
        $st = $pdo->prepare("SELECT id FROM rubricas WHERE hito_id = ? ORDER BY id ASC LIMIT 1");
        $st->execute([$selected_hito]);
        $rubrica_id = (int)$st->fetchColumn();
    }
}

/* ---------------------------
   Datos para combos
---------------------------- */
$estudiantes  = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos        = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();
$supervisores = $pdo->query("SELECT id, nombre, tipo FROM supervisores ORDER BY nombre")->fetchAll();
$rubricas     = $pdo->query("SELECT id, nombre FROM rubricas ORDER BY id")->fetchAll();

/* ---------------------------
   Criterios y niveles
---------------------------- */
$criterios = [];
$nivelesPorCriterio = []; // [criterio_id] => array de niveles (id, nombre, puntaje)

if ($rubrica_id > 0) {
    // criterios de la rúbrica
    $st = $pdo->prepare("SELECT id, nombre, orden FROM criterios WHERE rubrica_id = ? ORDER BY orden");
    $st->execute([$rubrica_id]);
    $criterios = $st->fetchAll();

    if ($criterios) {
        $ids = array_column($criterios, 'id');
        $in  = implode(',', array_fill(0, count($ids), '?'));

        // niveles disponibles por criterio (join evita consultas por opción)
        $sql = "
          SELECT cn.criterio_id, nl.id AS nivel_id, nl.nombre AS nivel_nombre, cn.puntaje
          FROM criterios_niveles cn
          JOIN niveles_logro nl ON nl.id = cn.nivel_logro_id
          WHERE cn.criterio_id IN ($in)
          ORDER BY cn.criterio_id, nl.id
        ";
        $st2 = $pdo->prepare($sql);
        $st2->execute($ids);
        while ($r = $st2->fetch()) {
            $cid = (int)$r['criterio_id'];
            if (!isset($nivelesPorCriterio[$cid])) $nivelesPorCriterio[$cid] = [];
            $nivelesPorCriterio[$cid][] = [
                'nivel_id'     => (int)$r['nivel_id'],
                'nivel_nombre' => $r['nivel_nombre'],
                'puntaje'      => (int)$r['puntaje'],
            ];
        }
    }
}

/* ---------------------------
   Contexto para banner
---------------------------- */
$ctx_est = $ctx_hito = null;
if ($selected_estudiante) {
    $q = $pdo->prepare("SELECT nombre FROM estudiantes WHERE id=?");
    $q->execute([$selected_estudiante]);
    $ctx_est = $q->fetchColumn();
}
if ($selected_hito) {
    $q = $pdo->prepare("SELECT nombre FROM hitos WHERE id=?");
    $q->execute([$selected_hito]);
    $ctx_hito = $q->fetchColumn();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Evaluación por Rúbrica</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script>
  function calcularTotal() {
    var total = 0;
    document.querySelectorAll('.nivel-logro').forEach(function(sel){
      var opt = sel.selectedOptions[0];
      if (opt && opt.dataset && opt.dataset.puntaje) {
        total += parseInt(opt.dataset.puntaje || '0', 10);
      }
    });
    var el = document.getElementById('total');
    if (el) el.innerText = total;
  }
  document.addEventListener('DOMContentLoaded', calcularTotal);
  </script>
</head>
<body class="container mt-4">

<h2 class="mb-3">Evaluación por Rúbrica</h2>

<?php if ($ctx_est || $ctx_hito || $tipo_param): ?>
  <div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
      <?php if ($ctx_est): ?>Estudiante: <strong><?= htmlspecialchars($ctx_est) ?></strong> &nbsp;<?php endif; ?>
      <?php if ($ctx_hito): ?>| Hito: <strong><?= htmlspecialchars($ctx_hito) ?></strong> &nbsp;<?php endif; ?>
      <?php if ($tipo_param): ?>| Tipo: <strong><?= htmlspecialchars(ucfirst($tipo_param)) ?></strong><?php endif; ?>
    </div>
    <div>
      <a href="evaluacion_rubrica.php" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    </div>
  </div>
<?php endif; ?>

<!-- Selector de rúbrica (permite cambiar) -->
<form method="GET" class="row g-3 mb-4">
  <div class="col-md-6">
    <label class="form-label">Selecciona Rúbrica</label>
    <select name="rubrica_id" class="form-select" onchange="this.form.submit()">
      <option value="">-- Seleccionar --</option>
      <?php foreach ($rubricas as $r): ?>
        <option value="<?= (int)$r['id'] ?>" <?= ($rubrica_id == $r['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($r['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Mantener filtros al cambiar de rúbrica -->
  <?php if ($selected_estudiante): ?><input type="hidden" name="estudiante_id" value="<?= (int)$selected_estudiante ?>"><?php endif; ?>
  <?php if ($selected_hito):       ?><input type="hidden" name="hito_id"       value="<?= (int)$selected_hito       ?>"><?php endif; ?>
  <?php if ($selected_supervisor): ?><input type="hidden" name="supervisor_id" value="<?= (int)$selected_supervisor ?>"><?php endif; ?>
  <?php if ($tipo_param):          ?><input type="hidden" name="tipo"          value="<?= htmlspecialchars($tipo_param) ?>"><?php endif; ?>
  <?php if ($evaluacion_id):       ?><input type="hidden" name="evaluacion_id" value="<?= (int)$evaluacion_id       ?>"><?php endif; ?>
</form>

<?php if ($selected_hito && $rubrica_id <= 0): ?>
  <div class="alert alert-warning">
    No se encontró una <strong>rúbrica</strong> para este hito/tipo. Crea una en <code>rubricas</code> o marca alguna como <strong>común</strong>.
  </div>
<?php endif; ?>

<?php if ($rubrica_id > 0 && $criterios): ?>
<form method="POST" action="guardar_evaluacion.php" class="mb-5">
  <input type="hidden" name="rubrica_id" value="<?= (int)$rubrica_id ?>">
  <?php if ($modo_edicion): ?>
    <input type="hidden" name="evaluacion_id" value="<?= (int)$evaluacion_id ?>">
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Estudiante</label>
      <select name="estudiante_id" class="form-select" required>
        <?php foreach ($estudiantes as $e): ?>
          <option value="<?= (int)$e['id'] ?>" <?= ($selected_estudiante && $e['id']==$selected_estudiante) ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Hito</label>
      <select name="hito_id" class="form-select" required>
        <?php foreach ($hitos as $h): ?>
          <option value="<?= (int)$h['id'] ?>" <?= ($selected_hito && $h['id']==$selected_hito) ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Supervisor</label>
      <select name="supervisor_id" class="form-select" required>
        <?php foreach ($supervisores as $s): ?>
          <option value="<?= (int)$s['id'] ?>" <?= ($selected_supervisor && $s['id']==$selected_supervisor) ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['nombre']) ?> (<?= htmlspecialchars($s['tipo']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <hr class="my-4">
  <h5 class="mb-3">Criterios de Evaluación</h5>

  <?php foreach ($criterios as $c): 
        $cid = (int)$c['id'];
        $niveles = $nivelesPorCriterio[$cid] ?? [];
  ?>
    <div class="mb-3">
      <label class="form-label"><?= htmlspecialchars($c['nombre']) ?></label>
      <select name="criterios[<?= $cid ?>]" class="form-select nivel-logro" onchange="calcularTotal()" required>
        <option value="">-- Seleccionar nivel --</option>
        <?php foreach ($niveles as $n):
              $sel = (isset($niveles_actuales[$cid]) && (int)$niveles_actuales[$cid] === (int)$n['nivel_id']) ? 'selected' : '';
        ?>
          <option value="<?= (int)$n['nivel_id'] ?>" data-puntaje="<?= (int)$n['puntaje'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($n['nivel_nombre']) ?> (<?= (int)$n['puntaje'] ?> pts)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endforeach; ?>

  <div class="mb-3">
    <label class="form-label">Observaciones generales</label>
    <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($observaciones_actuales) ?></textarea>
  </div>

  <div class="mb-3 fw-bold">
    Puntaje total: <span id="total">0</span> puntos
  </div>

  <button type="submit" class="btn btn-success">Guardar Evaluación</button>
  <a href="listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
</body>
</html>
