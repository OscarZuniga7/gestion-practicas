<?php
include('../includes/header.php');
include('../includes/db.php'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Informes</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Listado de Informes</h2>
    <a href="crear.php" class="btn btn-primary mb-3">Nuevo Informe</a>
    
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Hito</th>
                <th>Fecha Entrega</th>
                <th>Archivo</th>
                <th>Comentarios</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT informes.*, 
                       estudiantes.nombre AS nombre_estudiante, 
                       hitos.nombre AS nombre_hito
                FROM informes
                JOIN estudiantes ON informes.estudiante_id = estudiantes.id
                JOIN hitos ON informes.hito_id = hitos.id
                ORDER BY informes.fecha_entrega DESC";

        $stmt = $pdo->query($sql);
        while ($fila = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$fila['id']}</td>";
            echo "<td>" . htmlspecialchars($fila['nombre_estudiante']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre_hito']) . "</td>";
            echo "<td>{$fila['fecha_entrega']}</td>";
            $esUrl = filter_var($fila['archivo'], FILTER_VALIDATE_URL);
            $enlace = $esUrl ? $fila['archivo'] : "../archivos/{$fila['archivo']}";
            echo "<td><a href='{$enlace}' target='_blank'>Ver PDF</a></td>";
            echo "<td>" . nl2br(htmlspecialchars($fila['comentarios'])) . "</td>";
            echo "<td>
                    <a href='editar.php?id={$fila['id']}' class='btn btn-sm btn-warning mb-1'>Editar</a>
                    <a href='eliminar.php?id={$fila['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar este informe?\");'>Eliminar</a>
                  </td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS (opcional para funcionalidades como modales o tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
