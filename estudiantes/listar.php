<?php include('../includes/db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Estudiantes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2 class="mb-4">Lista de Estudiantes en Práctica</h2>
<a href="crear.php" class="btn btn-primary mb-3">Agregar Estudiante</a>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>RUT</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Carrera</th>
            <th>Teléfono</th>
            <th>Programa</th>
            <th>Asignatura</th>
            <th>Empresa</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Obtener estudiantes y sus empresas (si están asociadas)
        $stmt = $pdo->query("SELECT e.*, emp.nombre AS empresa_nombre
                             FROM estudiantes e
                             LEFT JOIN empresas emp ON e.empresa_id = emp.id
                             ORDER BY e.id DESC");

        while ($row = $stmt->fetch()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['rut'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['carrera'] ?></td>
            <td><?= $row['telefono'] ?></td>
            <td><?= $row['programa'] ?></td>
            <td><?= $row['asignatura'] ?></td>
            <td><?= $row['empresa_nombre'] ?? 'Sin asignar' ?></td>
            <td><?= $row['fecha_inicio'] ? date('d-m-Y', strtotime($row['fecha_inicio'])) : '' ?></td>
            <td><?= $row['fecha_fin'] ? date('d-m-Y', strtotime($row['fecha_fin'])) : '' ?></td>
            <td>
                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este estudiante?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
