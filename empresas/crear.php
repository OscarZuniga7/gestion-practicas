<?php 
include('../includes/db.php');
include('../includes/header.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Agregar Nueva Empresa</h2>

<form method="post">
    <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre de la empresa" required>
    <input class="form-control mb-2" type="text" name="rut" placeholder="RUT (opcional)">
    <input class="form-control mb-2" type="text" name="rubro" placeholder="Rubro o industria">
    <input class="form-control mb-2" type="text" name="direccion" placeholder="Dirección">
    <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono">
    <input class="form-control mb-2" type="email" name="email_contacto" placeholder="Correo de contacto">
    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $stmt = $pdo->prepare("INSERT INTO empresas (nombre, rut, rubro, direccion, telefono, email_contacto)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['rut'],
        $_POST['rubro'],
        $_POST['direccion'],
        $_POST['telefono'],
        $_POST['email_contacto']
    ]);
    echo "<div class='alert alert-success mt-3'>Empresa registrada exitosamente.</div>";
}
?>
<?php include('../includes/footer.php'); ?>
</body>
</html>
