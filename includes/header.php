<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Prácticas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/gestion-practicas/index.php">Gestión de Prácticas</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/estudiantes/listar.php">Estudiantes</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/empresas/listar.php">Empresas</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/supervisores/listar.php">Supervisores</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/hitos/listar.php">Hitos</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/informes/listar.php">Informes</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/evaluaciones/listar.php">Evaluaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/entrevistas/listar.php">Entrevistas</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navReportes" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Reportes
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navReportes">
            <li><a class="dropdown-item" href="/gestion-practicas/reportes/exportar_informe.php">Ver informe</a></li>
            <li><a class="dropdown-item" href="/gestion-practicas/reportes/exportar_informe.php?ini=2025-03-01&fin=2025-08-31&formato=xls">Descargar XLS 202420</a></li>
            <li><a class="dropdown-item" href="/gestion-practicas/reportes/exportar_informe.php?ini=2025-03-01&fin=2025-08-31&formato=csv">Descargar CSV 202420</a></li>
            <li><a class="dropdown-item" href="/gestion-practicas/reportes/exportar_informe_hitos.php">Informe por Hito</a></li>
            <li><a class="dropdown-item" href="/gestion-practicas/reportes/exportar_informe_completo.php">Informe Completo (hecho + pendientes)</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/reportes/alertas.php">Alertas</a></li>
        <li class="nav-item"><a class="nav-link" href="/gestion-practicas/informes/subir.php">Subir Informe</a></li>
      </ul>

    </div>
  </div>
</nav>
<div class="container">
