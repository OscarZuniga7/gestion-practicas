<?php 
include('../includes/db.php');
include('../includes/header.php'); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Supervisor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
    function toggleEmpresa() {
        const tipo = document.getElementById('tipo').value;
        document.getElementById('empresa-section').style.display = tipo === 'externo' ? 'block' : 'none';
    }
    </script>
</head>
<body class="container mt-5">

<h2>Agregar Nuevo Supervisor</h2>

<form method="post">
    <label class="form-label">Nombre</label>
    <input class="form-control mb-2" type="text" name="nombre" placeholder="Nombre completo" required>

    <label class="form-label">Tipo de Supervisor</label>
    <select class="form-select mb-2" name="tipo" id="tipo" onchange="toggleEmpresa()" required>
        <option value="">-- Seleccione tipo --</option>
        <option value="interno">Interno</option>
        <option value="externo">Externo</option>
    </select>

    <label class="form-label">Cargo</label>
    <input class="form-control mb-2" type="text" name="cargo" placeholder="Cargo o función" required>

    <label class="form-label">Correo Electrónico</label>
    <input class="form-control mb-2" type="email" name="email" placeholder="Correo electrónico" required>

    <label class="form-label">Teléfono</label>
    <input class="form-control mb-2" type="text" name="telefono" placeholder="Teléfono de contacto" required>

    <div id="empresa-section" style="display: none;">
        <label class="form-label">Empresa (solo para externos)</label>
        <select class="form-select mb-2" name="empresa_id">
            <option value="">-- Seleccione una empresa --</option>
            <?php
            $empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre");
            foreach ($empresas as $e):
            ?>
                <option value="<?= $e['id'] ?>"><?= $e['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cargo = $_POST['cargo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $empresa_id = ($tipo === 'externo') ? ($_POST['empresa_id'] ?: null) : null;

    $stmt = $pdo->prepare("INSERT INTO supervisores 
        (nombre, tipo, cargo, email, telefono, empresa_id)
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->execute([$nombre, $tipo, $cargo, $email, $telefono, $empresa_id]);

    echo "<div class='alert alert-success mt-3'>Supervisor registrado exitosamente.</div>";
}
?>

<?php include('../includes/footer.php'); ?>
</body>
</html>
