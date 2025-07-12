<?php
include('../includes/db.php');
include('../includes/header.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<div class='alert alert-danger'>ID de supervisor no especificado.</div>";
    exit;
}

// Obtener supervisor actual
$stmt = $pdo->prepare("SELECT * FROM supervisores WHERE id = ?");
$stmt->execute([$id]);
$supervisor = $stmt->fetch();

if (!$supervisor) {
    echo "<div class='alert alert-danger'>Supervisor no encontrado.</div>";
    exit;
}

// Obtener empresas
$empresas = $pdo->query("SELECT id, nombre FROM empresas ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Procesar actualización
if (isset($_POST['actualizar'])) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $cargo = $_POST['cargo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $empresa_id = ($tipo === 'externo') ? ($_POST['empresa_id'] ?: null) : null;

    $stmt = $pdo->prepare("UPDATE supervisores SET 
        nombre = ?, tipo = ?, cargo = ?, email = ?, telefono = ?, empresa_id = ?
        WHERE id = ?");

    $stmt->execute([$nombre, $tipo, $cargo, $email, $telefono, $empresa_id, $id]);

    echo "<div class='alert alert-success mt-3'>Supervisor actualizado exitosamente.</div>";

    // Refrescar datos actualizados
    $stmt = $pdo->prepare("SELECT * FROM supervisores WHERE id = ?");
    $stmt->execute([$id]);
    $supervisor = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Supervisor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
    function toggleEmpresa() {
        const tipo = document.getElementById('tipo').value;
        document.getElementById('empresa-section').style.display = (tipo === 'externo') ? 'block' : 'none';
    }
    window.addEventListener('DOMContentLoaded', () => {
        toggleEmpresa(); // Ejecutar al cargar
    });
    </script>
</head>
<body class="container mt-5">

<h2>Editar Supervisor</h2>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input class="form-control" type="text" name="nombre" value="<?= htmlspecialchars($supervisor['nombre']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tipo</label>
        <select class="form-select" name="tipo" id="tipo" onchange="toggleEmpresa()" required>
            <option value="interno" <?= $supervisor['tipo'] === 'interno' ? 'selected' : '' ?>>Interno</option>
            <option value="externo" <?= $supervisor['tipo'] === 'externo' ? 'selected' : '' ?>>Externo</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Cargo</label>
        <input class="form-control" type="text" name="cargo" value="<?= htmlspecialchars($supervisor['cargo']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Correo Electrónico</label>
        <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($supervisor['email']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input class="form-control" type="text" name="telefono" value="<?= htmlspecialchars($supervisor['telefono']) ?>" required>
    </div>

    <div class="mb-3" id="empresa-section">
        <label class="form-label">Empresa (solo si es externo)</label>
        <select class="form-select" name="empresa_id">
            <option value="">-- Seleccione una empresa --</option>
            <?php foreach ($empresas as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $e['id'] == $supervisor['empresa_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="btn btn-primary" type="submit" name="actualizar">Actualizar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php include('../includes/footer.php'); ?>
</body>
</html>
