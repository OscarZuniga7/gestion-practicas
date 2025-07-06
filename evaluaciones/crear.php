<?php
require_once('../includes/db.php');
include('../includes/header.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $estudiante_id = $_POST['estudiante_id'];
    $hito_id = $_POST['hito_id'];
    $supervisor = $_POST['supervisor'];
    $nota = $_POST['nota'];
    $fecha_evaluacion = $_POST['fecha_evaluacion'];
    $observaciones = $_POST['observaciones'];
    $archivo_url = $_POST['archivo_url'];

    $sql = "INSERT INTO evaluaciones (estudiante_id, hito_id, supervisor, nota, fecha_evaluacion, observaciones, archivo)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estudiante_id, $hito_id, $supervisor, $nota, $fecha_evaluacion, $observaciones, $archivo_url]);

    header("Location: listar.php");
    exit;
}

$estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Evaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Nueva Evaluación</h2>
    <form method="POST">
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
            <select name="hito_id" class="form-select">
                <option value="">-- Ninguno --</option>
                <?php foreach ($hitos as $h): ?>
                    <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Supervisor</label>
            <input type="text" name="supervisor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nota</label>
            <input type="number" name="nota" step="0.1" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha Evaluación</label>
            <input type="date" name="fecha_evaluacion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">URL del archivo PDF</label>
            <input type="url" name="archivo_url" class="form-control" placeholder="https://...">
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
