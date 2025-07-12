<?php
include('../includes/header.php');
include('../includes/db.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supervisores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

<h2 class="mb-4">Lista de Supervisores</h2>
<a href="crear.php" class="btn btn-primary mb-3">Agregar Supervisor</a>

<table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Cargo</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Empresa</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Obtener todos los supervisores con nombre de empresa si corresponde
        $stmt = $pdo->query("SELECT s.*, e.nombre AS empresa_nombre
                             FROM supervisores s
                             LEFT JOIN empresas e ON s.empresa_id = e.id
                             ORDER BY s.id DESC");

        while ($row = $stmt->fetch()):
            $tipo = ucfirst($row['tipo']); // 'interno' o 'externo'
            $empresa = ($row['tipo'] === 'externo') ? ($row['empresa_nombre'] ?? 'Sin empresa') : 'No aplica';
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $tipo ?></td>
            <td><?= $row['cargo'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['telefono'] ?></td>
            <td><?= $empresa ?></td>
            <td>
                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="eliminar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este supervisor?')">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
</body>
</html>
