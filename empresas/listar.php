<?php include('../includes/db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Empresas Registradas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2 class="mb-4">Lista de Empresas</h2>
<a href="crear.php" class="btn btn-primary mb-3">Agregar Nueva Empresa</a>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>RUT</th>
            <th>Rubro</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Email Contacto</th>
            <th>Fecha Registro</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM empresas ORDER BY id DESC");
        while ($row = $stmt->fetch()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['rut'] ?></td>
            <td><?= $row['rubro'] ?></td>
            <td><?= $row['direccion'] ?></td>
            <td><?= $row['telefono'] ?></td>
            <td><?= $row['email_contacto'] ?></td>
            <td><?= date('d-m-Y', strtotime($row['fecha_registro'])) ?></td>
            <td>
                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta empresa?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
