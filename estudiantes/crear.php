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
    <input class="form-control mb-2" type="text" name="programa" placeholder="Programa (ej: UNAB12100)">
    <input class="form-control mb-2" type="text" name="asignatura" placeholder="Asignatura (ej: PRACTICA I)">
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
    <label class="form-label">Fecha de Inicio</label>
    <input class="form-control mb-2" type="date" name="fecha_inicio" placeholder="Fecha de inicio">
    <label class="form-label">Fecha de Fin</label>
    <input class="form-control mb-2" type="date" name="fecha_fin" placeholder="Fecha de término">

    <button class="btn btn-success" type="submit" name="guardar">Guardar</button>
    <a class="btn btn-secondary" href="listar.php">Volver</a>
</form>

<?php
if (isset($_POST['guardar'])) {
    $stmt = $pdo->prepare("INSERT INTO estudiantes 
    (rut, nombre, email, carrera, telefono, programa, asignatura, empresa_id, fecha_inicio, fecha_fin)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $_POST['rut'],
    $_POST['nombre'],
    $_POST['email'],
    $_POST['carrera'],
    $_POST['telefono'],
    $_POST['programa'],
    $_POST['asignatura'],
    $_POST['empresa_id'] ?: null,
    $_POST['fecha_inicio'],
    $_POST['fecha_fin']
]);

    echo "<div class='alert alert-success mt-3'>Estudiante agregado correctamente.</div>";
}
?>

</body>
</html>
