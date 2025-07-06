<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    die("ID de informe no proporcionado.");
}

$id = $_GET['id'];
$mensaje = "";

// Obtener el informe actual
$stmt = $pdo->prepare("SELECT * FROM informes WHERE id = ?");
$stmt->execute([$id]);
$informe = $stmt->fetch();

if (!$informe) {
    die("Informe no encontrado.");
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'];
    $hito_id = $_POST['hito_id'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $archivo = $_POST['archivo'];
    $comentarios = $_POST['comentarios'];

    $sql = "UPDATE informes 
            SET estudiante_id = ?, hito_id = ?, fecha_entrega = ?, archivo = ?, comentarios = ? 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estudiante_id, $hito_id, $fecha_entrega, $archivo, $comentarios, $id]);

    $mensaje = "Informe actualizado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Informe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Editar Informe</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="estudiante_id" class="form-label">Estudiante</label>
            <select name="estudiante_id" id="estudiante_id" class="form-select" required>
                <?php
                $estudiantes = $pdo->query("SELECT id, nombre FROM estudiantes ORDER BY nombre")->fetchAll();
                foreach ($estudiantes as $e) {
                    $selected = $e['id'] == $informe['estudiante_id'] ? 'selected' : '';
                    echo "<option value='{$e['id']}' $selected>{$e['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="hito_id" class="form-label">Hito</label>
            <select name="hito_id" id="hito_id" class="form-select" required>
                <?php
                $hitos = $pdo->query("SELECT id, nombre FROM hitos")->fetchAll();
                foreach ($hitos as $h) {
                    $selected = $h['id'] == $informe['hito_id'] ? 'selected' : '';
                    echo "<option value='{$h['id']}' $selected>{$h['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
            <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control" value="<?= $informe['fecha_entrega'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="archivo" class="form-label">Nombre de Archivo</label>
            <input type="text" name="archivo" class="form-control" value="<?= $informe['archivo'] ?>" placeholder="Ej: hito1.pdf o URL de SharePoint" required>
            <small class="form-text text-muted">
            Puedes editar el nombre del archivo o ingresar una URL externa (como SharePoint).
            </small>
        </div>

        <div class="mb-3">
            <label for="comentarios" class="form-label">Comentarios</label>
            <textarea name="comentarios" id="comentarios" class="form-control" rows="3"><?= $informe['comentarios'] ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="listar.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
