<?php
require_once('../includes/db.php');
include('../includes/header.php');

$evaluacion_id = $_GET['evaluacion_id'] ?? null;
$rubrica_id = $_GET['rubrica_id'] ?? null;
$modo_edicion = false;
$selected_estudiante = null;
$selected_hito = null;
$selected_supervisor = null;
$niveles_actuales = [];
$observaciones_actuales = '';

if ($evaluacion_id) {
    $modo_edicion = true;
    // Obtener rubrica_id desde los criterios
    $stmt = $pdo->prepare("SELECT c.rubrica_id, e.estudiante_id, e.hito_id, e.supervisor, e.observaciones
                           FROM evaluaciones e
                           JOIN evaluaciones_criterios ec ON ec.evaluacion_id = e.id
                           JOIN criterios c ON c.id = ec.criterio_id
                           WHERE e.id = ? LIMIT 1");
    $stmt->execute([$evaluacion_id]);
    $datos_edicion = $stmt->fetch();

    if ($datos_edicion) {
        $rubrica_id = $datos_edicion['rubrica_id'];
        $selected_estudiante = $datos_edicion['estudiante_id'];
        $selected_hito = $datos_edicion['hito_id'];
        $selected_supervisor = $datos_edicion['supervisor'];
        $observaciones_actuales = $datos_edicion['observaciones'];

        // Cargar niveles por criterio existentes
        $stmt = $pdo->prepare("SELECT criterio_id, nivel_logro_id FROM evaluaciones_criterios WHERE evaluacion_id = ?");
        $stmt->execute([$evaluacion_id]);
        $niveles_actuales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

$estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();
$supervisores = $pdo->query("SELECT id, nombre, tipo FROM supervisores ORDER BY nombre")->fetchAll();
$rubricas = $pdo->query("SELECT id, nombre FROM rubricas ORDER BY id")->fetchAll();

$criterios = [];
$niveles = [];
if ($rubrica_id) {
    $stmt = $pdo->prepare("SELECT * FROM criterios WHERE rubrica_id = ? ORDER BY orden");
    $stmt->execute([$rubrica_id]);
    $criterios = $stmt->fetchAll();

    $niveles = $pdo->query("SELECT * FROM niveles_logro ORDER BY id")->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluación por Rúbrica</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
    function calcularTotal() {
        let total = 0;
        const selects = document.querySelectorAll('.nivel-logro');
        selects.forEach(select => {
            total += parseInt(select.selectedOptions[0].dataset.puntaje || 0);
        });
        document.getElementById('total').innerText = total;
    }
    </script>
</head>
<body class="container mt-5">
<h2>Evaluación por Rúbrica</h2>

<form method="GET" class="row mb-4">
    <div class="col-md-6">
        <label class="form-label">Selecciona Rúbrica</label>
        <select name="rubrica_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Seleccionar --</option>
            <?php foreach ($rubricas as $r): ?>
                <option value="<?= $r['id'] ?>" <?= ($rubrica_id == $r['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<?php if ($rubrica_id && $criterios): ?>
<form method="POST" action="guardar_evaluacion.php">
    <input type="hidden" name="rubrica_id" value="<?= $rubrica_id ?>">
    <?php if ($modo_edicion): ?>
        <input type="hidden" name="evaluacion_id" value="<?= $evaluacion_id ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Estudiante</label>
        <select name="estudiante_id" class="form-select" required>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= ($e['id'] == $selected_estudiante) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Hito</label>
        <select name="hito_id" class="form-select" required>
            <?php foreach ($hitos as $h): ?>
                <option value="<?= $h['id'] ?>" <?= ($h['id'] == $selected_hito) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($h['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Supervisor</label>
        <select name="supervisor_id" class="form-select" required>
            <?php foreach ($supervisores as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($s['id'] == $selected_supervisor) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nombre']) ?> (<?= $s['tipo'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <hr>
    <h5>Criterios de Evaluación</h5>

    <?php foreach ($criterios as $c): ?>
        <div class="mb-3">
            <label class="form-label"><?= htmlspecialchars($c['nombre']) ?></label>
            <select name="criterios[<?= $c['id'] ?>]" class="form-select nivel-logro" onchange="calcularTotal()" required>
                <option value="">-- Seleccionar nivel --</option>
                <?php foreach ($niveles as $n):
                    $stmt = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
                    $stmt->execute([$c['id'], $n['id']]);
                    $puntaje = $stmt->fetchColumn();
                    if ($puntaje === false) continue;
                ?>
                    <option value="<?= $n['id'] ?>" data-puntaje="<?= $puntaje ?>" <?= (isset($niveles_actuales[$c['id']]) && $niveles_actuales[$c['id']] == $n['id']) ? 'selected' : '' ?>>
                        <?= $n['nombre'] ?> (<?= $puntaje ?> pts)
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
