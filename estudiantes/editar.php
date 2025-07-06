<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    die('ID no proporcionado.');
}

$id = $_GET['id'];

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE estudiantes SET 
        rut = ?, nombre = ?, email = ?, carrera = ?, telefono = ?,
        programa = ?, asignatura = ?, empresa_id = ?, fecha_inicio = ?, fecha_fin = ?
        WHERE id = ?");

    $stmt->execute([
        $_POST['rut'],
        $_POST['nombre'],
        $_POST['email'],
        $_POST['carrera'],
        $_POST['telefono'],
        $_POST['programa'],
        $_POST['asignatura'],
        $_POST['empresa_id'],
        $_POST['fecha_inicio'],
        $_POST['fecha_fin'],
        $id
    ]);

    header('Location: listar.php');
    exit();
}

// Obtener datos del estudiante
$stmt = $pdo->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->execute([$id]);
$estudiante = $stmt->fetch();

if (!$estudiante) {
    die('Estudiante no encontrado.');
}
?>

<h2>Editar Estudiante</h2>

<form method="post">
    <input class="form-control mb-2" type="text" name="rut" value="<?= $estudiante['rut'] ?>" required>
    <input class="form-control mb-2" type="text" name="nombre" value="<?= $estudiante['nombre'] ?>" required>
    <input class="form-control mb-2" type="email" name="email" value="<?= $estudiante['email'] ?>" required>
    <input class="form-control mb-2" type="text" name="carrera" value="<?= $estudiante['carrera'] ?>">
    <input class="form-control mb-2" type="text" name="telefono" value="<?= $estudiante['telefono'] ?>">
    <input class="form-control mb-2" type="text" name="programa" value="<?= $estudiante['programa'] ?>" placeholder="Programa">
    <input class="form-control mb-2" type="text" name="asignatura" value="<?= $estudiante['asignatura'] ?>" placeholder="Asignatura">

    <label class="form-label">Empresa</label>
    <select class="form-select mb-2" name="empresa_id" required>
        <option value="">-- Seleccione una empresa --</option>
        <?php
        $empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre");
        foreach ($empresas as $e):
            $selected = ($e['id'] == $estudiante['empresa_id']) ? 'selected' : '';
        ?>
            <option value="<?= $e['id'] ?>" <?= $selected ?>><?= $e['nombre'] ?></option>
        <?php endforeach; ?>
    </select>

    <input class="form-control mb-2" type="date" name="fecha_inicio" value="<?= $estudiante['fecha_inicio'] ?>">
    <input class="form-control mb-2" type="date" name="fecha_fin" value="<?= $estudiante['fecha_fin'] ?>">

    <button class="btn btn-success" type="submit">Actualizar</button>
    <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form>

<?php include('../includes/footer.php'); ?>
