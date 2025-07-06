<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    die('ID de empresa no proporcionado.');
}

$id = $_GET['id'];

// Procesar actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE empresas SET
        nombre = ?, rut = ?, rubro = ?, direccion = ?, telefono = ?, email_contacto = ?
        WHERE id = ?");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['rut'],
        $_POST['rubro'],
        $_POST['direccion'],
        $_POST['telefono'],
        $_POST['email_contacto'],
        $id
    ]);
    header('Location: listar.php');
    exit();
}

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->execute([$id]);
$empresa = $stmt->fetch();

if (!$empresa) {
    die('Empresa no encontrada.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Editar Empresa</h2>

<form method="post">
    <label class="form-label">Nombre</label>
    <input class="form-control mb-2" type="text" name="nombre" value="<?= $empresa['nombre'] ?>" required>

    <label class="form-label">RUT</label>
    <input class="form-control mb-2" type="text" name="rut" value="<?= $empresa['rut'] ?>">

    <label class="form-label">Rubro</label>
    <input class="form-control mb-2" type="text" name="rubro" value="<?= $empresa['rubro'] ?>">

    <label class="form-label">Dirección</label>
    <input class="form-control mb-2" type="text" name="direccion" value="<?= $empresa['direccion'] ?>">

    <label class="form-label">Teléfono</label>
    <input class="form-control mb-2" type="text" name="telefono" value="<?= $empresa['telefono'] ?>">

    <label class="form-label">Correo de Contacto</label>
    <input class="form-control mb-2" type="email" name="email_contacto" value="<?= $empresa['email_contacto'] ?>">

    <button class="btn btn-success" type="submit">Actualizar</button>
    <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form>
<?php include('../includes/footer.php'); ?>
</body>
</html>
