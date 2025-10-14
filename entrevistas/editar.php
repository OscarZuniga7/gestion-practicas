<?php
// entrevistas/editar.php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  exit('ID de entrevista no proporcionado.');
}

/* --------- Cargar entrevista --------- */
$st = $pdo->prepare("SELECT * FROM entrevistas WHERE id = ?");
$st->execute([$id]);
$entrevista = $st->fetch();
if (!$entrevista) {
  http_response_code(404);
  exit('Entrevista no encontrada.');
}

$msg = $_GET['msg'] ?? null;

/* --------- POST: actualizar --------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $estudiante_id   = isset($_POST['estudiante_id']) ? (int)$_POST['estudiante_id'] : 0;
  $hito_id         = isset($_POST['hito_id'])       ? (int)$_POST['hito_id']       : 0;
  $fecha           = trim($_POST['fecha'] ?? '');

  // supervisor opcional
  $supervisor_id   = (isset($_POST['supervisor_id']) && $_POST['supervisor_id'] !== '')
                      ? (int)$_POST['supervisor_id'] : null;

  $modalidad       = trim($_POST['modalidad'] ?? '');
  $evidencia_url   = trim($_POST['evidencia_url'] ?? '');
  // aceptar 'comentarios' o 'comentario' del form
  $comentarios     = trim($_POST['comentarios'] ?? ($_POST['comentario'] ?? ''));

  $tipo_supervisor = trim($_POST['tipo_supervisor'] ?? '');

  // Validaciones básicas
  if ($estudiante_id <= 0 || $hito_id <= 0 || $fecha === '') {
    header("Location: editar.php?id=$id&msg=Faltan+datos+obligatorios");
    exit;
  }

  $dt = DateTime::createFromFormat('Y-m-d', $fecha);
  if (!$dt || $dt->format('Y-m-d') !== $fecha) {
    header("Location: editar.php?id=$id&msg=Fecha+inválida");
    exit;
  }

  // Normalizar tipo_supervisor
  if ($tipo_supervisor !== '') {
    $tipo_supervisor = strtolower($tipo_supervisor);
    if (!in_array($tipo_supervisor, ['interno','externo'], true)) {
      $tipo_supervisor = null;
    }
  } else {
    $tipo_supervisor = null;
  }

  // Si no vino tipo_supervisor pero sí supervisor, intentar inferirlo
  if (!$tipo_supervisor && $supervisor_id) {
    $q = $pdo->prepare('SELECT tipo FROM supervisores WHERE id = ?');
    $q->execute([$supervisor_id]);
    $tipo_supervisor = $q->fetchColumn() ?: null; // 'interno' | 'externo'
  }

  // Campos opcionales a NULL si vienen vacíos
  $modalidad     = ($modalidad !== '') ? $modalidad : null;
  $evidencia_url = ($evidencia_url !== '') ? $evidencia_url : null;
  $comentarios   = ($comentarios !== '') ? $comentarios : null;

  // UPDATE
  $sql = "UPDATE entrevistas
          SET estudiante_id = :estudiante_id,
              hito_id       = :hito_id,
              supervisor_id = :supervisor_id,
              fecha         = :fecha,
              modalidad     = :modalidad,
              evidencia_url = :evidencia_url,
              comentarios   = :comentarios,
              tipo_supervisor = :tipo_supervisor
          WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':estudiante_id',  $estudiante_id, PDO::PARAM_INT);
  $stmt->bindValue(':hito_id',        $hito_id,       PDO::PARAM_INT);
  ($supervisor_id === null)
    ? $stmt->bindValue(':supervisor_id', null, PDO::PARAM_NULL)
    : $stmt->bindValue(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
  $stmt->bindValue(':fecha',          $fecha,         PDO::PARAM_STR);
  ($modalidad === null)
    ? $stmt->bindValue(':modalidad', null, PDO::PARAM_NULL)
    : $stmt->bindValue(':modalidad', $modalidad, PDO::PARAM_STR);
  ($evidencia_url === null)
    ? $stmt->bindValue(':evidencia_url', null, PDO::PARAM_NULL)
    : $stmt->bindValue(':evidencia_url', $evidencia_url, PDO::PARAM_STR);
  ($comentarios === null)
    ? $stmt->bindValue(':comentarios', null, PDO::PARAM_NULL)
    : $stmt->bindValue(':comentarios', $comentarios, PDO::PARAM_STR);
  ($tipo_supervisor === null)
    ? $stmt->bindValue(':tipo_supervisor', null, PDO::PARAM_NULL)
    : $stmt->bindValue(':tipo_supervisor', $tipo_supervisor, PDO::PARAM_STR);
  $stmt->bindValue(':id',             $id,            PDO::PARAM_INT);

  try {
    $stmt->execute();
    header('Location: listar.php?ok=entrevista_actualizada&msg=Entrevista+actualizada+correctamente');
    exit;
  } catch (PDOException $e) {
    header("Location: editar.php?id=$id&msg=Error+al+actualizar+la+entrevista");
    exit;
  }
}

/* --------- Datos para selects --------- */
$estudiantes  = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos        = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();
$supervisores = $pdo->query("SELECT id, nombre, tipo FROM supervisores ORDER BY nombre")->fetchAll();

?>
<div class="container mt-4">
  <h2 class="mb-3">Editar Entrevista</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Estudiante</label>
      <select class="form-select" name="estudiante_id" required>
        <option value="">Seleccione estudiante</option>
        <?php foreach ($estudiantes as $e): ?>
          <option value="<?= (int)$e['id'] ?>"
            <?= ($e['id'] == $entrevista['estudiante_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Hito</label>
      <select class="form-select" name="hito_id" required>
        <option value="">Seleccione hito</option>
        <?php foreach ($hitos as $h): ?>
          <option value="<?= (int)$h['id'] ?>"
            <?= ($h['id'] == $entrevista['hito_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($h['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Fecha</label>
      <input class="form-control" type="date" name="fecha"
             value="<?= htmlspecialchars($entrevista['fecha']) ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Modalidad</label>
      <input class="form-control" type="text" name="modalidad"
             value="<?= htmlspecialchars($entrevista['modalidad'] ?? '') ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label">URL Evidencia</label>
      <input class="form-control" type="url" name="evidencia_url"
             value="<?= htmlspecialchars($entrevista['evidencia_url'] ?? '') ?>">
    </div>

    <div class="col-12">
      <label class="form-label">Comentarios</label>
      <textarea class="form-control" name="comentarios" rows="3"><?= htmlspecialchars($entrevista['comentarios'] ?? '') ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">Supervisor Externo</label>
      <select class="form-select" name="supervisor_id" id="supervisor_id">
        <option value="">Sin supervisor externo</option>
        <?php foreach ($supervisores as $s): ?>
          <option value="<?= (int)$s['id'] ?>"
                  data-tipo="<?= htmlspecialchars($s['tipo']) ?>"
                  <?= ($s['id'] == $entrevista['supervisor_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['nombre']) ?> (<?= htmlspecialchars($s['tipo']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="tipo_supervisor" class="form-label">Tipo de Supervisor</label>
      <?php $tipoSel = strtolower($entrevista['tipo_supervisor'] ?? ''); ?>
      <select name="tipo_supervisor" id="tipo_supervisor" class="form-select">
        <option value="">— inferir / sin especificar —</option>
        <option value="interno" <?= $tipoSel === 'interno' ? 'selected' : '' ?>>Interno</option>
        <option value="externo" <?= $tipoSel === 'externo' ? 'selected' : '' ?>>Externo</option>
      </select>
      <div class="form-text">Si eliges un supervisor con tipo definido, se inferirá automáticamente.</div>
    </div>

    <div class="col-12">
      <button class="btn btn-primary">Actualizar</button>
      <a class="btn btn-secondary" href="listar.php">Cancelar</a>
    </div>
  </form>
</div>

<script>
// Si el supervisor seleccionado tiene un 'data-tipo', sugiere ese tipo en el select
document.addEventListener('DOMContentLoaded', function () {
  var selSup = document.getElementById('supervisor_id');
  var selTipo = document.getElementById('tipo_supervisor');
  if (!selSup || !selTipo) return;

  selSup.addEventListener('change', function () {
    var opt = selSup.selectedOptions[0];
    if (!opt) return;
    var t = (opt.getAttribute('data-tipo') || '').toLowerCase();
    if (t === 'interno' || t === 'externo') {
      selTipo.value = t;
    }
  }, false);
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
