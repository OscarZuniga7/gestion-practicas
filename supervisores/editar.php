<?php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    die('ID no proporcionado.');
}

$id = $_GET['id'];

// Procesar actualización si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE supervisores SET
        nombre = ?, cargo = ?, email = ?, telefono = ?, empresa_id = ?
        WHERE id = ?");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['cargo'],
        $_POST['email'],
        $_POST['telefono'],
        $_POST['empresa_id'] ?: null,
        $id
    ]);
    header('Location: listar.php');
    exit();
}

// Obtener datos actuales del supervisor
$stmt = $pdo->prepare("SELECT * FROM supervisores WHERE id = ?");
$stmt->execute([$id]);
$supervisor = $stmt->fetch();

if (!$supervisor) {
    die('Supervisor no encontrado.');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Supervisor Externo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Editar Supervisor Externo</h2>

<form method="post">
    <label class="form-label">Nombre</label>
    <input class="form-control mb-2" type="text" name="nombre" value="<?= $supervisor['nombre'] ?>" required>

    <label class="form-label">Cargo</label>
    <input class="form-control mb-2" type="text" name="cargo" value="<?= $supervisor['cargo'] ?>">

    <label class="form-label">Correo Electrónico</label>
    <input class="form-control mb-2" type="email" name="email" value="<?= $supervisor['email'] ?>">

    <label class="form-label">Teléfono</label>
    <input class="form-control mb-2" type="text" name="telefono" value="<?= $supervisor['telefono'] ?>">

    <label class="form-label">Empresa</label>
    <select class="form-select mb-2" name="empresa_id">
        <option value="">-- Seleccione una empresa --</option>
        <?php
        $empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre");
        foreach ($empresas as $e):
            $selected = $e['id'] == $supervisor['empresa_id'] ? 'selected' : '';
        ?>
            <option value="<?= $e['id'] ?>" <?= $selected ?>><?= $e['nombre'] ?></option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-success" type="submit">Actualizar</button>
    <a class="btn btn-secondary" href="listar.php">Cancelar</a>
</form>

</body>
</html>
