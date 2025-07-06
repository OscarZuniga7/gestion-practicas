<?php
include('../includes/header.php');
include('../includes/db.php'); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Evaluaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Listado de Evaluaciones</h2>
    <a href="crear.php" class="btn btn-primary mb-3">Nueva Evaluación</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Hito</th>
                <th>Supervisor</th>
                <th>Nota</th>
                <th>Fecha Evaluación</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT evaluaciones.*, 
                       estudiantes.nombre AS nombre_estudiante, 
                       hitos.nombre AS nombre_hito
                FROM evaluaciones
                JOIN estudiantes ON evaluaciones.estudiante_id = estudiantes.id
                LEFT JOIN hitos ON evaluaciones.hito_id = hitos.id
                ORDER BY evaluaciones.fecha_evaluacion DESC";

        $stmt = $pdo->query($sql);
        while ($fila = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$fila['id']}</td>";
            echo "<td>{$fila['nombre_estudiante']}</td>";
            echo "<td>" . ($fila['nombre_hito'] ?? '-') . "</td>";
            echo "<td>{$fila['supervisor']}</td>";
            echo "<td>{$fila['nota']}</td>";
            echo "<td>{$fila['fecha_evaluacion']}</td>";
            echo "<td>{$fila['observaciones']}</td>";
            echo "<td>
                    <a href='editar.php?id={$fila['id']}' class='btn btn-sm btn-warning'>Editar</a>
                    <a href='eliminar.php?id={$fila['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar esta evaluación?\");'>Eliminar</a>
                  </td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
