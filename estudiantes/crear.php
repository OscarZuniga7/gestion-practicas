<?php include('../includes/db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Estudiante</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Agregar Nuevo Estudiante</h2>

<form action="" method="post">
    <input class="form-control mb-2" type="text" name="rut" placeholder="RUT" required>
    <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre completo" required>
    <input class="form-control mb-2" type="email" name="email" placeholder="Correo" required>
    <input class="form-control mb-2" type="text" name="carrera" placeholder="Carrera">
    <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono">
    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $stmt = $pdo->prepare("INSERT INTO estudiantes (rut, nombre, email, carrera, telefono) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['rut'],
        $_POST['nombre'],
        $_POST['email'],
        $_POST['carrera'],
        $_POST['telefono']
    ]);
    echo "<div class='alert alert-success mt-3'>Estudiante agregado correctamente.</div>";
}
?>

</body>
</html>
