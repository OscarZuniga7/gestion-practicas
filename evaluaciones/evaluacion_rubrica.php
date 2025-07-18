<?php
require_once('../includes/db.php');
include('../includes/header.php');

// Obtener datos iniciales
$estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();
$supervisores = $pdo->query("SELECT id, nombre, tipo FROM supervisores ORDER BY nombre")->fetchAll();
$rubricas = $pdo->query("SELECT id, nombre FROM rubricas ORDER BY id")->fetchAll();

// Si se selecciona una rúbrica
$rubrica_id = $_GET['rubrica_id'] ?? null;
$criterios = [];
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

    <div class="mb-3">
        <label class="form-label">Estudiante</label>
        <select name="estudiante_id" class="form-select" required>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Hito</label>
        <select name="hito_id" class="form-select" required>
            <?php foreach ($hitos as $h): ?>
                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Supervisor</label>
        <select name="supervisor_id" class="form-select" required>
            <?php foreach ($supervisores as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?> (<?= $s['tipo'] ?>)</option>
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
                    // Buscar el puntaje específico desde criterios_niveles
                    $stmt = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
                    $stmt->execute([$c['id'], $n['id']]);
                    $puntaje = $stmt->fetchColumn();
                    if ($puntaje === false) continue; // ignorar si no hay combinación
                ?>
                    <option value="<?= $n['id'] ?>" data-puntaje="<?= $puntaje ?>">
                        <?= $n['nombre'] ?> (<?= $puntaje ?> pts)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endforeach; ?>

    <div class="mb-3">
        <label class="form-label">Observaciones generales</label>
        <textarea name="observaciones" class="form-control" rows="3"></textarea>
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
