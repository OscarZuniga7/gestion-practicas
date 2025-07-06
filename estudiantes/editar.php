<?php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    die('ID no proporcionado.');
}

$id = $_GET['id'];

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE estudiantes SET 
    rut = ?, nombre = ?, email = ?, carrera = ?, telefono = ?,
    programa = ?, asignatura = ?, empresa = ?, fecha_inicio = ?, fecha_fin = ?
    WHERE id = ?");

    $stmt->execute([
        $_POST['rut'],
        $_POST['nombre'],
        $_POST['email'],
        $_POST['carrera'],
        $_POST['telefono'],
        $_POST['programa'],
        $_POST['asignatura'],
        $_POST['empresa'],
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

<!DOCTYPE html>
<html>
<head>
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Editar Estudiante</h2>

<form method="post">
    <input class="form-control mb-2" type="text" name="rut" value="<?= $estudiante['rut'] ?>" required>
    <input class="form-control mb-2" type="text" name="nombre" value="<?= $estudiante['nombre'] ?>" required>
    <input class="form-control mb-2" type="email" name="email" value="<?= $estudiante['email'] ?>" required>
    <input class="form-control mb-2" type="text" name="carrera" value="<?= $estudiante['carrera'] ?>">
    <input class="form-control mb-2" type="text" name="telefono" value="<?= $estudiante['telefono'] ?>">
    <input class="form-control mb-2" type="text" name="programa" value="<?= $estudiante['programa'] ?>" placeholder="Programa">
    <input class="form-control mb-2" type="text" name="asignatura" value="<?= $estudiante['asignatura'] ?>" placeholder="Asignatura">
    <input class="form-control mb-2" type="text" name="empresa" value="<?= $estudiante['empresa'] ?>" placeholder="Empresa">
    <input class="form-control mb-2" type="date" name="fecha_inicio" value="<?= $estudiante['fecha_inicio'] ?>">
    <input class="form-control mb-2" type="date" name="fecha_fin" value="<?= $estudiante['fecha_fin'] ?>">

    <button class="btn btn-success" type="submit">Actualizar</button>
    <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form>

</body>
</html>
