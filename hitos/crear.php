<?php 
include('../includes/db.php');
include('../includes/header.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Hito</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Agregar Nuevo Hito</h2>

<form method="post">
    <label class="form-label">Nombre del Hito</label>
    <input class="form-control mb-2" type="text" name="nombre" placeholder="Ej: Hito 3, Evaluación Parcial" required>

    <label class="form-label">Descripción</label>
    <textarea class="form-control mb-2" name="descripcion" rows="3" placeholder="Breve descripción del hito"></textarea>

    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $stmt = $pdo->prepare("INSERT INTO hitos (nombre, descripcion) VALUES (?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion']
    ]);
    echo "<div class='alert alert-success mt-3'>Hito registrado exitosamente.</div>";
}
?>
<?php include('../includes/footer.php'); ?>
</body>
</html>
