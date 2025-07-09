<?php
// entrevistas/editar.php
include('../includes/db.php');
include('../includes/header.php');
$tipo_supervisor = 'interno';

if (!isset($_GET['id'])) {
    die('ID de entrevista no proporcionado.');
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE entrevistas SET estudiante_id = ?, hito_id = ?, fecha = ?, modalidad = ?, evidencia_url = ?, comentarios = ?, supervisor_id = ?, tipo_supervisor = ? WHERE id = ?");
    $stmt->execute([
        $_POST['estudiante_id'],
        $_POST['hito_id'],
        $_POST['fecha'],
        $_POST['modalidad'],
        $_POST['evidencia_url'],
        $_POST['comentarios'],
        $_POST['supervisor_id'] ?: null,
        $_POST['tipo_supervisor'],
        $id
    ]);

    header('Location: listar.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM entrevistas WHERE id = ?");
$stmt->execute([$id]);
$entrevista = $stmt->fetch();

$estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre");
$hitos = $pdo->query("SELECT id, nombre FROM hitos ORDER BY nombre");
$supervisores = $pdo->query("SELECT id, nombre FROM supervisores ORDER BY nombre");
?>

<div class="container mt-5">
    <h2>Editar Entrevista</h2>
    <form method="post">
        <label class="form-label">Estudiante</label>
        <select class="form-select mb-2" name="estudiante_id" required>
            <option value="">Seleccione estudiante</option>
            <?php foreach ($estudiantes as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $e['id'] == $entrevista['estudiante_id'] ? 'selected' : '' ?>><?= $e['nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <label class="form-label">Hito</label>
        <select class="form-select mb-2" name="hito_id" required>
            <option value="">Seleccione hito</option>
            <?php foreach ($hitos as $h): ?>
                <option value="<?= $h['id'] ?>" <?= $h['id'] == $entrevista['hito_id'] ? 'selected' : '' ?>><?= $h['nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <label class="form-label">Fecha</label>
        <input class="form-control mb-2" type="date" name="fecha" value="<?= $entrevista['fecha'] ?>" required>

        <label class="form-label">Modalidad</label>
        <input class="form-control mb-2" type="text" name="modalidad" value="<?= $entrevista['modalidad'] ?>">

        <label class="form-label">URL Evidencia</label>
        <input class="form-control mb-2" type="url" name="evidencia_url" value="<?= $entrevista['evidencia_url'] ?>">

        <label class="form-label">Comentarios</label>
        <textarea class="form-control mb-2" name="comentarios"><?= $entrevista['comentarios'] ?></textarea>

        <label class="form-label">Supervisor Externo</label>
        <select class="form-select mb-3" name="supervisor_id">
            <option value="">Sin supervisor externo</option>
            <?php foreach ($supervisores as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $s['id'] == $entrevista['supervisor_id'] ? 'selected' : '' ?>><?= $s['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
        <div class="mb-3">
        <label for="tipo_supervisor" class="form-label">Tipo de Supervisor</label>
        <select name="tipo_supervisor" class="form-select" required>
            <option value="interno" <?= $tipo_supervisor == 'interno' ? 'selected' : '' ?>>Interno</option>
            <option value="externo" <?= $tipo_supervisor == 'externo' ? 'selected' : '' ?>>Externo</option>
        </select>
</div>


        <button class="btn btn-primary">Actualizar</button>
        <a class="btn btn-secondary" href="listar.php">Cancelar</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
