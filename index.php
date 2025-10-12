<?php
include('includes/db.php');
include('includes/header.php');

// Contadores globales (como ya tenías)
$totalEstudiantes  = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
$totalEmpresas     = $pdo->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
$totalSupervisores = $pdo->query("SELECT COUNT(*) FROM supervisores")->fetchColumn();
$totalInformes     = $pdo->query("SELECT COUNT(*) FROM informes")->fetchColumn();
$totalEvaluaciones = $pdo->query("SELECT COUNT(*) FROM evaluaciones")->fetchColumn();
$totalEntrevistas  = $pdo->query("SELECT COUNT(*) FROM entrevistas")->fetchColumn();

/* --------- NUEVO: parámetros por defecto para reportes --------- */
$iniDefault = '2025-03-01';
$finDefault = '2025-10-31';
$qs = http_build_query(['ini'=>$iniDefault, 'fin'=>$finDefault]);

/* --------- NUEVO: contadores de pendientes (para chips) --------- */
$stmt = $pdo->prepare("
  SELECT
    SUM(informe_fecha     IS NULL)                                                   AS pend_informes,
    SUM(evaluacion_fecha  IS NULL)                                                   AS pend_evaluaciones,
    SUM(entrevista_fecha  IS NULL)                                                   AS pend_entrevistas,
    SUM( (informe_fecha IS NULL) OR (evaluacion_fecha IS NULL) OR (entrevista_fecha IS NULL) ) AS pend_total
  FROM vw_informe_supervision_completo
  WHERE fecha_fin BETWEEN :ini AND :fin
");

$stmt->execute([':ini'=>$iniDefault, ':fin'=>$finDefault]);
$pend = $stmt->fetch() ?: ['pend_informes'=>0,'pend_evaluaciones'=>0,'pend_entrevistas'=>0,'pend_total'=>0];
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


    <!-- ===== NUEVA CARD DE REPORTES ===== -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="mb-0">Reportes</h5>
            <div class="small text-muted">
              Periodo: <strong><?=htmlspecialchars($iniDefault)?></strong> a <strong><?=htmlspecialchars($finDefault)?></strong>
            </div>
          </div>
        </div>
        <div class="card-body">

          <!-- Chips de pendientes -->
          <div class="mb-3 d-flex flex-wrap gap-2">
            <span class="badge bg-secondary">Pendientes totales: <?=$pend['pend_total']?></span>
            <span class="badge bg-info text-dark">Informes: <?=$pend['pend_informes']?></span>
            <span class="badge bg-danger">Evaluaciones: <?=$pend['pend_evaluaciones']?></span>
            <span class="badge bg-warning text-dark">Entrevistas: <?=$pend['pend_entrevistas']?></span>
          </div>

          <!-- Línea 1: Consolidado -->
          <div class="mb-3">
            <h6 class="mb-2">Consolidado (último por estudiante)</h6>
            <div class="d-flex flex-wrap gap-2">
              <a class="btn btn-outline-dark" href="reportes/exportar_informe.php?<?=$qs?>">Ver</a>
              <a class="btn btn-success" href="reportes/exportar_informe.php?<?=$qs?>&formato=xls">Descargar XLS</a>
              <a class="btn btn-outline-secondary" href="reportes/exportar_informe.php?<?=$qs?>&formato=csv">Descargar CSV</a>
            </div>
          </div>

          <hr>

          <!-- Línea 2: Por Hito -->
          <div class="mb-3">
            <h6 class="mb-2">Por Hito (todas las evaluaciones)</h6>
            <div class="d-flex flex-wrap gap-2">
              <a class="btn btn-outline-dark" href="reportes/exportar_informe_hitos.php?<?=$qs?>">Ver</a>
              <a class="btn btn-success" href="reportes/exportar_informe_hitos.php?<?=$qs?>&formato=xls">Descargar XLS</a>
              <a class="btn btn-outline-secondary" href="reportes/exportar_informe_hitos.php?<?=$qs?>&formato=csv">Descargar CSV</a>
            </div>
          </div>

          <hr>

          <!-- Línea 3: Completo (hecho + pendientes) -->
          <div class="mb-1">
            <h6 class="mb-2">Completo (hecho + pendientes)</h6>
            <div class="d-flex flex-wrap gap-2">
              <a class="btn btn-outline-dark" href="reportes/exportar_informe_completo.php?<?=$qs?>">Ver</a>
              <a class="btn btn-outline-primary" href="reportes/exportar_informe_completo.php?<?=$qs?>&pendientes=1">Ver solo pendientes</a>
              <a class="btn btn-success" href="reportes/exportar_informe_completo.php?<?=$qs?>&formato=xls">Descargar XLS</a>
              <a class="btn btn-outline-secondary" href="reportes/exportar_informe_completo.php?<?=$qs?>&formato=csv">Descargar CSV</a>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- ===== FIN CARD REPORTES ===== -->

  </div>
</div>

<?php include('includes/footer.php'); ?>
