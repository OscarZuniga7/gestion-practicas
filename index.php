<?php
include('includes/db.php');
include('includes/header.php');

// Obtener contadores globales
$totalEstudiantes = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
$totalEmpresas = $pdo->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
$totalSupervisores = $pdo->query("SELECT COUNT(*) FROM supervisores")->fetchColumn();
$totalInformes = $pdo->query("SELECT COUNT(*) FROM informes")->fetchColumn();
$totalEvaluaciones = $pdo->query("SELECT COUNT(*) FROM evaluaciones")->fetchColumn();
$totalEntrevistas = $pdo->query("SELECT COUNT(*) FROM entrevistas")->fetchColumn();
?>

<div class="container mt-5">
  <h1 class="mb-4">Dashboard de Gestión de Prácticas</h1>
  <div class="row g-4">

    <div class="col-md-4">
      <div class="card border-primary shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Estudiantes</h5>
          <p class="display-6"><?= $totalEstudiantes ?></p>
          <a href="estudiantes/listar.php" class="btn btn-outline-primary">Ver Estudiantes</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-success shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Empresas</h5>
          <p class="display-6"><?= $totalEmpresas ?></p>
          <a href="empresas/listar.php" class="btn btn-outline-success">Ver Empresas</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-warning shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Supervisores</h5>
          <p class="display-6"><?= $totalSupervisores ?></p>
          <a href="supervisores/listar.php" class="btn btn-outline-warning">Ver Supervisores</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-info shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Informes</h5>
          <p class="display-6"><?= $totalInformes ?></p>
          <a href="informes/listar.php" class="btn btn-outline-info">Ver Informes</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-danger shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Evaluaciones</h5>
          <p class="display-6"><?= $totalEvaluaciones ?></p>
          <a href="evaluaciones/listar.php" class="btn btn-outline-danger">Ver Evaluaciones</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-secondary shadow-sm">
        <div class="card-body text-center">
          <h5 class="card-title">Entrevistas</h5>
          <p class="display-6"><?= $totalEntrevistas ?></p>
          <a href="entrevistas/listar.php" class="btn btn-outline-secondary">Ver Entrevistas</a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include('includes/footer.php'); ?>
