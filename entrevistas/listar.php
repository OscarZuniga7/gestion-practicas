<?php
include('../includes/db.php');
include('../includes/header.php');
?>

<div class="container mt-5">
    <h2>Listado de Entrevistas</h2>
    <a href="crear.php" class="btn btn-primary mb-3">Nueva Entrevista</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Supervisor</th>
                <th>Tipo Supervisor</th>
                <th>Fecha</th>
                <th>Modalidad</th>
                <th>Comentarios</th>
                <th>Acta / Evidencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT entrevistas.*, 
                       estudiantes.nombre AS nombre_estudiante,
                       supervisores.nombre AS nombre_supervisor
                FROM entrevistas
                JOIN estudiantes ON entrevistas.estudiante_id = estudiantes.id
                JOIN supervisores ON entrevistas.supervisor_id = supervisores.id
                ORDER BY entrevistas.fecha DESC";
        $stmt = $pdo->query($sql);
        while ($fila = $stmt->fetch()):
        ?>
            <tr>
                <td><?= $fila['id'] ?></td>
                <td><?= $fila['nombre_estudiante'] ?></td>
                <td><?= $fila['nombre_supervisor'] ?></td>
                <td><?= ucfirst($fila['tipo_supervisor']) ?></td>
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['modalidad'] ?></td>
                <td><?= $fila['comentarios'] ?></td>
                <td>
                    <?php
                    if (filter_var($fila['evidencia_url'], FILTER_VALIDATE_URL)) {
                        echo "<a href='{$fila['evidencia_url']}' target='_blank'>Ver Acta</a>";
                    } elseif ($fila['evidencia_url']) {
                        echo "<a href='../archivos/{$fila['evidencia_url']}' target='_blank'>{$fila['evidencia_url']}</a>";
                    } else {
                        echo "<span class='text-muted'>Sin evidencia</span>";
                    }
                    ?>
                </td>
                <td>
                    <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta entrevista?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
