<?php
include('includes/db.php');

// Obtener contadores globales
$totalEstudiantes = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
$totalEmpresas = $pdo->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
$totalSupervisores = $pdo->query("SELECT COUNT(*) FROM supervisores")->fetchColumn();
$totalInformes = $pdo->query("SELECT COUNT(*) FROM informes")->fetchColumn();
$totalEvaluaciones = $pdo->query("SELECT COUNT(*) FROM evaluaciones")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gestión de Prácticas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Dashboard de Gestión de Prácticas</h1>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">Estudiantes</h5>
                    <p class="display-6"><?= $totalEstudiantes ?></p>
                    <a href="estudiantes/listar.php" class="btn btn-outline-primary">Ver</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="card-title">Empresas</h5>
                    <p class="display-6"><?= $totalEmpresas ?></p>
                    <a href="empresas/listar.php" class="btn btn-outline-success">Ver</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title">Supervisores</h5>
                    <p class="display-6"><?= $totalSupervisores ?></p>
                    <a href="supervisores/listar.php" class="btn btn-outline-warning">Ver</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Informes</h5>
                    <p class="display-6"><?= $totalInformes ?></p>
                    <a href="informes/listar.php" class="btn btn-outline-info">Ver</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title">Evaluaciones</h5>
                    <p class="display-6"><?= $totalEvaluaciones ?></p>
                    <a href="evaluaciones/listar.php" class="btn btn-outline-danger">Ver</a>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
