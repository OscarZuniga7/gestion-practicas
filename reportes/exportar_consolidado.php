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
$sql = "SELECT
          estudiante,
          rut,
          estudiante_email,
          empresa,
          practica,
          DATE_FORMAT(inicio,'%d-%m-%Y') AS inicio,
          DATE_FORMAT(fin,'%d-%m-%Y')    AS fin,

          /* Informes */
          informe_hito1_url,
          informe_hito1_fecha,
          /* REEMPLAZO: ahora el texto de nota */
          nota_rubrica_texto_hito1_interno AS nota_hito1_interno,
          informe_hito2_url,
          informe_hito2_fecha,
          nota_rubrica_texto_hito2_interno AS nota_hito2_interno,

          /* Entrevista / Acta */
          entrevista_fecha,
          evidencia_url,
          acta_pdf_url,

          /* Supervisor externo */
          supervisor_externo_nombre,
          supervisor_externo_email,
          supervisor_externo_telefono,
          supervisor_externo_cargo

        FROM vw_reporte_consolidado";

// --------- WHERE dinámico ---------
$w = [];
$p = [];
$w[]       = "fin BETWEEN :ini AND :fin";
$p[':ini'] = $ini;
$p[':fin'] = $fin;

if ($practica !== '') {
  $w[]            = "practica = :practica";
  $p[':practica'] = $practica;
}

if ($soloCerr) {
  $w[] = "(acta_pdf_url IS NOT NULL OR (nota_hito1_interno IS NOT NULL OR nota_hito2_interno IS NOT NULL))";
}

if ($w) $sql .= " WHERE " . implode(" AND ", $w);
$sql .= " ORDER BY estudiante, practica";

// --------- Ejecutar ---------
$stmt = $pdo->prepare($sql);
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

// --------- Helpers HTML (solo para la tabla web) ---------
function renderLinkOrDash(?string $url, string $label): string {
  $url = trim((string)$url);
  if ($url === '') return '—';
  $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
  $lab  = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
  return '<a href="'.$safe.'" target="_blank" rel="noopener noreferrer">'.$lab.'</a>';
}

// Mapeo de columnas URL => etiqueta visible
$linkLabels = [
  'informe_hito1_url' => 'Ver informe hito 1',
  'informe_hito2_url' => 'Ver informe hito 2',
  'evidencia_url'     => 'Ver evidencia',
  'acta_pdf_url'      => 'Ver Acta',
];
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Consolidado de Prácticas (todo en uno)</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
  .table a { text-decoration: none; }
  .table a:hover { text-decoration: underline; }
</style>
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
    <table class="table table-sm table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <?php if (!empty($rows)) foreach (array_keys($rows[0]) as $c) echo "<th>".htmlspecialchars($c)."</th>"; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)) {
          foreach ($rows as $r) {
            echo "<tr>";
            foreach ($r as $k => $v) {
              // Si es una de las columnas de URL, mostrar el link "limpio"
              if (isset($linkLabels[$k])) {
                echo "<td>" . renderLinkOrDash($v, $linkLabels[$k]) . "</td>";
              } else {
                echo "<td>" . htmlspecialchars((string)$v) . "</td>";
              }
            }
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
