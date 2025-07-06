<?php include('../includes/db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Agregar Supervisor Externo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2>Agregar Nuevo Supervisor Externo</h2>

<form method="post">
    <label class="form-label">Nombre</label>
    <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre completo" required>

    <label class="form-label">Cargo</label>
    <input class="form-control mb-2" type="text" name="cargo" placeholder="Cargo o función en la empresa">

    <label class="form-label">Correo Electrónico</label>
    <input class="form-control mb-2" type="email" name="email" placeholder="Correo electrónico">

    <label class="form-label">Teléfono</label>
    <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono de contacto">

    <label class="form-label">Empresa</label>
    <select class="form-select mb-2" name="empresa_id">
        <option value="">-- Seleccione una empresa --</option>
        <?php
        $empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre");
        foreach ($empresas as $e):
        ?>
            <option value="<?= $e['id'] ?>"><?= $e['nombre'] ?></option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $stmt = $pdo->prepare("INSERT INTO supervisores 
        (nombre, cargo, email, telefono, empresa_id)
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['cargo'],
        $_POST['email'],
        $_POST['telefono'],
        $_POST['empresa_id'] ?: null
    ]);
    echo "<div class='alert alert-success mt-3'>Supervisor registrado exitosamente.</div>";
}
?>

</body>
</html>
