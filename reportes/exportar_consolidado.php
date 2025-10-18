<?php
// reportes/exportar_consolidado.php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';

// --------- Filtros ---------
$ini        = $_GET['ini']        ?? '2025-03-01';
$fin        = $_GET['fin']        ?? '2025-12-31';
$practica   = $_GET['practica']   ?? '';          // 'PRACTICA I' / 'PRACTICA II'
$soloCerr   = isset($_GET['solo_cerradas']) ? (int)$_GET['solo_cerradas'] : 0; // opcional
$formato    = $_GET['formato']    ?? 'html';      // html | csv | xls
$debug      = isset($_GET['debug']) ? (int)$_GET['debug'] : 0;

// --------- SQL base ---------
// Cambia el nombre de la vista si corresponde
$sql = "SELECT
          estudiante, rut, estudiante_email,
          empresa,
          practica, hito,
          DATE_FORMAT(fecha_inicio,'%d-%m-%Y') AS inicio,
          DATE_FORMAT(fecha_fin,'%d-%m-%Y')    AS fin,
          -- Informes
          estado_informe_h1, informe_h1_url, nota_h1_texto,
          estado_informe_h2, informe_h2_url, nota_h2_texto,
          -- Entrevista / Acta
          estado_entrevista, entrevista_fecha,
          acta_pdf_url,
          grabacion_url,
          -- Supervisor externo
          supervisor_ext_nombre, supervisor_ext_email, supervisor_ext_telefono,
          -- Marca temporal
          DATE_FORMAT(ultima_actualizacion,'%d-%m-%Y') AS ultima_actualizacion
        FROM vw_reporte_consolidado";

// --------- WHERE dinámico bien formado ---------
$w = [];
$p = [];

// rango por fecha_fin (o ajusta a la columna que uses de corte)
$w[]       = "fecha_fin BETWEEN :ini AND :fin";
$p[':ini'] = $ini;
$p[':fin'] = $fin;

if ($practica !== '') {
  $w[]             = "practica = :practica";
  $p[':practica']  = $practica; // <-- nombre del placeholder = clave en $p
}

if ($soloCerr) {
  // ejemplo: solo casos con acta o con notas en ambos hitos (ajusta a tu lógica)
  $w[] = "(acta_pdf_url IS NOT NULL OR (nota_h1_texto IS NOT NULL AND nota_h2_texto IS NOT NULL))";
}

if ($w) {
  $sql .= " WHERE " . implode(" AND ", $w);
}

$sql .= " ORDER BY estudiante, practica, hito";

// --------- Ejecutar de forma segura ---------
$stmt = $pdo->prepare($sql);

// DEBUG opcional para cazar HY093 si te vuelve a pasar
if ($debug) {
  echo "<pre><b>SQL:</b>\n" . $sql . "\n\n<b>Params:</b>\n";
  var_dump($p);
  echo "</pre>";
}
$stmt->execute($p);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --------- Exportaciones ---------
if ($formato === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=consolidado.csv');
  $out = fopen('php://output', 'w');
  if (!empty($rows)) {
    fputcsv($out, array_keys($rows[0]));
    foreach ($rows as $r) fputcsv($out, $r);
  } else {
    fputcsv($out, ['Sin datos']);
  }
  fclose($out);
  exit;
}

if ($formato === 'xls') {
  header("Content-Type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename=consolidado.xls");
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
<title>Consolidado de Prácticas (todo en uno)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
  <h1 class="mb-3">Consolidado de Prácticas</h1>

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
    <div class="col-md-3 d-flex align-items-end">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="solo_cerradas" value="1" id="chkC" <?= $soloCerr ? 'checked':''; ?>>
        <label class="form-check-label" for="chkC">Sólo cerradas</label>
      </div>
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a class="btn btn-success" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&solo_cerradas=<?=$soloCerr?>&formato=xls">Descargar XLS</a>
      <a class="btn btn-outline-secondary" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&solo_cerradas=<?=$soloCerr?>&formato=csv">CSV</a>
      <a class="btn btn-outline-dark" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&solo_cerradas=<?=$soloCerr?>&debug=1">Debug</a>
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
        } else {
          echo "<tr><td>Sin datos para los filtros seleccionados</td></tr>";
        } ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
