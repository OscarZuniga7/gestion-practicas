<?php
require_once __DIR__ . '/../includes/db.php';

$ini      = $_GET['ini']      ?? '2025-03-01';
$fin      = $_GET['fin']      ?? '2025-08-31';
$practica = $_GET['practica'] ?? '';
$estId    = $_GET['estudiante_id'] ?? '';
$formato  = $_GET['formato']  ?? 'html';

$sql = "SELECT 
          estudiante, rut, email, empresa,
          practica, hito,                  -- hito justo después de practica
          DATE_FORMAT(fecha_inicio,'%d-%m-%Y') AS inicio,
          DATE_FORMAT(fecha_fin,'%d-%m-%Y')    AS fin,
          estado_informe,
          DATE_FORMAT(informe_fecha,'%d-%m-%Y')    AS informe_fecha,
          estado_evaluacion,
          DATE_FORMAT(evaluacion_fecha,'%d-%m-%Y') AS evaluacion_fecha,
          nota_rubrica_texto, nota_rubrica_pct,
          estado_entrevista,
          DATE_FORMAT(entrevista_fecha,'%d-%m-%Y')  AS entrevista_fecha,
          DATE_FORMAT(ultima_actualizacion,'%d-%m-%Y') AS ultima_actualizacion
        FROM vw_informe_supervision_por_hito
        WHERE fecha_fin BETWEEN :ini AND :fin";

$params = [':ini'=>$ini, ':fin'=>$fin];

if ($practica !== '') {
  $sql .= " AND practica = :practica";
  $params[':practica'] = $practica;
}
if ($estId !== '') {
  $sql .= " AND id_estudiante = :est";
  $params[':est'] = $estId;
}

$sql .= " ORDER BY estudiante, hito, evaluacion_fecha";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

if ($formato === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=informe_por_hito.csv');
  $out = fopen('php://output', 'w');
  if (!empty($rows)) {
    fputcsv($out, array_keys($rows[0]));
    foreach ($rows as $r) fputcsv($out, $r);
  } else fputcsv($out, ['Sin datos']);
  fclose($out);
  exit;
}

if ($formato === 'xls') {
  header("Content-Type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename=informe_por_hito.xls");
  echo "<table border='1'><tr>";
  if (!empty($rows)) {
    foreach (array_keys($rows[0]) as $c) echo "<th>".htmlspecialchars($c)."</th>";
    echo "</tr>";
    foreach ($rows as $r) {
      echo "<tr>";
      foreach ($r as $v) echo "<td>".htmlspecialchars((string)$v)."</td>";
      echo "</tr>";
    }
  } else {
    echo "<th>Sin datos</th></tr>";
  }
  echo "</table>";
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Informe por Hito</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
  <h1 class="mb-3">Informe por Hito (todas las evaluaciones)</h1>

  <form class="row g-3 mb-4" method="get">
    <div class="col-md-3">
      <label class="form-label">Fecha fin (desde)</label>
      <input type="date" name="ini" value="<?=htmlspecialchars($ini)?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Fecha fin (hasta)</label>
      <input type="date" name="fin" value="<?=htmlspecialchars($fin)?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Práctica</label>
      <select name="practica" class="form-select">
        <option value="">(Todas)</option>
        <option value="PRACTICA I"  <?= $practica==='PRACTICA I' ? 'selected':''; ?>>PRACTICA I</option>
        <option value="PRACTICA II" <?= $practica==='PRACTICA II'? 'selected':''; ?>>PRACTICA II</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Estudiante (ID)</label>
      <input type="number" name="estudiante_id" value="<?=htmlspecialchars($estId)?>" class="form-control" placeholder="(opcional)">
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a class="btn btn-success" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&estudiante_id=<?=urlencode($estId)?>&formato=xls">Descargar XLS</a>
      <a class="btn btn-outline-secondary" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&estudiante_id=<?=urlencode($estId)?>&formato=csv">CSV</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm table-striped table-bordered">
      <thead class="table-light">
        <tr>
          <?php if (!empty($rows)) foreach (array_keys($rows[0]) as $c) echo "<th>".htmlspecialchars($c)."</th>"; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)) {
          foreach ($rows as $r) {
            echo "<tr>";
            foreach ($r as $v) echo "<td>".htmlspecialchars((string)$v)."</td>";
            echo "</tr>";
          }
        } else { echo "<tr><td>Sin datos para los filtros seleccionados</td></tr>"; } ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
