<?php
include('../includes/db.php');
include('../includes/header.php');

// Validar que venga un ID
if (!isset($_GET['id'])) {
    die("ID de hito no especificado.");
}

// Obtener datos del hito
$stmt = $pdo->prepare("SELECT * FROM hitos WHERE id = ?");
$stmt->execute([$_GET['id']]);
$hito = $stmt->fetch();

if (!$hito) {
    die("Hito no encontrado.");
}

// Procesar formulario
if (isset($_POST['guardar'])) {
    $nuevoNombre = $_POST['nombre'];
    $nuevaDescripcion = $_POST['descripcion'];

    $update = $pdo->prepare("UPDATE hitos SET nombre = ?, descripcion = ? WHERE id = ?");
    $update->execute([$nuevoNombre, $nuevaDescripcion, $_GET['id']]);

    echo "<div class='alert alert-success'>Hito actualizado correctamente.</div>";
    // Redirigir
    echo "<meta http-equiv='refresh' content='2;url=listar.php'>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Hito</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Editar Hito</h2>

<form method="post">
    <label class="form-label">Nombre del Hito</label>
    <input class="form-control mb-2" type="text" name="nombre" value="<?= htmlspecialchars($hito['nombre']) ?>" required>

    <label class="form-label">Descripción</label>
    <textarea class="form-control mb-3" name="descripcion" rows="3"><?= htmlspecialchars($hito['descripcion']) ?></textarea>

    <button class="btn btn-primary" type="submit" name="guardar">Actualizar</button>
    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include('../includes/footer.php'); ?>
</body>
</html>
