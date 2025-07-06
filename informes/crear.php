<?php
include('../includes/db.php');
include('../includes/header.php');

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'];
    $hito_id = $_POST['hito_id'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $archivo = $_POST['archivo'];
    $comentarios = $_POST['comentarios'];

    $sql = "INSERT INTO informes (estudiante_id, hito_id, fecha_entrega, archivo, comentarios) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estudiante_id, $hito_id, $fecha_entrega, $archivo, $comentarios]);

    $mensaje = "Informe agregado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Informe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Nuevo Informe</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                <option value="">Seleccione un estudiante</option>
                <?php
                $estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
                foreach ($estudiantes as $e) {
                    echo "<option value='{$e['id']}'>{$e['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="hito_id" class="form-label">Hito</label>
            <select name="hito_id" id="hito_id" class="form-select" required>
                <option value="">Seleccione un hito</option>
                <?php
                $hitos = $pdo->query("SELECT id, nombre FROM hitos")->fetchAll();
                foreach ($hitos as $h) {
                    echo "<option value='{$h['id']}'>{$h['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
            <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="archivo">Ruta del archivo:</label>
            <input type="text" name="archivo" class="form-control" placeholder="Ej: hito1.pdf o URL de SharePoint" required>
            <small class="form-text text-muted">
            Puedes ingresar un nombre de archivo (si está en /archivos/) o una URL completa (OneDrive/SharePoint).
            </small>
        </div>


        <div class="mb-3">
            <label for="comentarios" class="form-label">Comentarios</label>
            <textarea name="comentarios" id="comentarios" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="listar.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
