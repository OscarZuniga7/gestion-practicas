<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    echo "ID de evaluación no especificado.";
    exit;
}

$id = $_GET['id'];

// Obtener datos de la evaluación
$stmt = $pdo->prepare("SELECT * FROM evaluaciones WHERE id = ?");
$stmt->execute([$id]);
$evaluacion = $stmt->fetch();

if (!$evaluacion) {
    echo "Evaluación no encontrada.";
    exit;
}

// Obtener estudiantes e hitos
$estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
$hitos = $pdo->query("SELECT id, nombre FROM hitos ORDER BY id")->fetchAll();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudiante_id = $_POST['estudiante_id'];
    $hito_id = $_POST['hito_id'] ?: null;
    $supervisor = $_POST['supervisor'];
    $nota = $_POST['nota'];
    $observaciones = $_POST['observaciones'];
    $archivo = $_POST['archivo'];
    $fecha_evaluacion = $_POST['fecha_evaluacion'];

    $sql = "UPDATE evaluaciones SET 
                estudiante_id = ?, hito_id = ?, supervisor = ?, 
                nota = ?, observaciones = ?, archivo = ?, 
                fecha_evaluacion = ?
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $estudiante_id, $hito_id, $supervisor,
        $nota, $observaciones, $archivo,
        $fecha_evaluacion, $id
    ]);

    header("Location: listar.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Evaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Evaluación</h2>
    <form method="post">
        <div class="mb-3">
            <label>Estudiante</label>
            <select name="estudiante_id" class="form-select" required>
                <?php foreach ($estudiantes as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $e['id'] == $evaluacion['estudiante_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Hito (opcional)</label>
            <select name="hito_id" class="form-select">
                <option value="">-- Ninguno --</option>
                <?php foreach ($hitos as $h): ?>
                    <option value="<?= $h['id'] ?>" <?= $h['id'] == $evaluacion['hito_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($h['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Supervisor</label>
            <input type="text" name="supervisor" class="form-control" value="<?= htmlspecialchars($evaluacion['supervisor']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Nota</label>
            <input type="number" step="0.01" name="nota" class="form-control" value="<?= $evaluacion['nota'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Observaciones</label>
            <textarea name="observaciones" class="form-control"><?= htmlspecialchars($evaluacion['observaciones']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>URL del PDF (OneDrive o SharePoint)</label>
            <input type="url" name="archivo" class="form-control" value="<?= htmlspecialchars($evaluacion['archivo']) ?>" placeholder="https://...">
        </div>

        <div class="mb-3">
            <label>Fecha de evaluación</label>
            <input type="date" name="fecha_evaluacion" class="form-control" value="<?= $evaluacion['fecha_evaluacion'] ?>">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="listar.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
<?php
// Verifica si la evaluación tiene criterios evaluados por rúbrica
$sql_rubrica = "SELECT COUNT(*) FROM evaluaciones_criterios WHERE evaluacion_id = ?";
$stmt_rubrica = $pdo->prepare($sql_rubrica);
$stmt_rubrica->execute([$id]);
$tiene_rubrica = $stmt_rubrica->fetchColumn() > 0;

if ($tiene_rubrica):
    echo '<a href="../evaluaciones/evaluacion_rubrica.php?evaluacion_id=' . $id . '" class="btn btn-warning mt-3">Editar Rúbrica</a>';
endif;
?>
<?php include('../includes/footer.php'); ?>
</body>
</html>
