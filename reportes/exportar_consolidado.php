<?php
// reportes/exportar_consolidado.php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';
include('../includes/header.php');

/* --------- Filtros --------- */
$ini        = $_GET['ini']        ?? '2025-03-01';
$fin        = $_GET['fin']        ?? '2025-12-31';
$practica   = $_GET['practica']   ?? '';          // 'PRACTICA I' / 'PRACTICA II'
$soloCerr   = isset($_GET['solo_cerradas']) ? (int)$_GET['solo_cerradas'] : 0;
$formato    = $_GET['formato']    ?? 'html';      // html | csv | xls
$debug      = isset($_GET['debug']) ? (int)$_GET['debug'] : 0;

/* --------- SQL --------- */
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

$stmt = $pdo->prepare($sql);
if ($debug) {
  echo "<pre><b>SQL:</b>\n" . $sql . "\n\n<b>Params:</b>\n";
  var_dump($p);
  echo "</pre>";
}
$stmt->execute($p);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --------- Exportes servidor (se mantienen) --------- */
if ($formato === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=consolidado.csv');
  $out = fopen('php://output', 'w');
  if (!empty($rows)) {
    fputcsv($out, array_keys($rows[0]));
    foreach ($rows as $r) fputcsv($out, $r);
  } else fputcsv($out, ['Sin datos']);
  fclose($out); exit;
}

if ($formato === 'xls') {
  header("Content-Type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename=consolidado.xls");
  echo "<table border='1'><tr>";
  if (!empty($rows)) {
    foreach (array_keys($rows[0]) as $c) echo "<th>".htmlspecialchars($c)."</th>";
    echo "</tr>";
    foreach ($rows as $r) { echo "<tr>";
      foreach ($r as $v) echo "<td>".htmlspecialchars((string)$v)."</td>";
      echo "</tr>";
    }
  } else { echo "<th>Sin datos</th></tr>"; }
  echo "</table>"; exit;
}

/* --------- Helpers de render --------- */
function renderLinkOrDash(?string $url, string $label): string {
  $url = trim((string)$url);
  if ($url === '') return '—';
  $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
  $lab  = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
  return '<a href="'.$safe.'" target="_blank" rel="noopener noreferrer">'.$lab.'</a>';
}

function badgePractica(string $p): string {
  $p = strtoupper(trim($p));
  $cls = ($p === 'PRACTICA II' || $p === 'PRÁCTICA II') ? 'bg-primary' : 'bg-secondary';
  return '<span class="badge '.$cls.'">'.$p.'</span>';
}

function mailOrDash(?string $mail): string {
  $mail = trim((string)$mail);
  if ($mail === '') return '—';
  $safe = htmlspecialchars($mail, ENT_QUOTES, 'UTF-8');
  return '<a href="mailto:'.$safe.'">'.$safe.'</a>';
}

$linkLabels = [
  'informe_hito1_url' => 'Ver informe hito 1',
  'informe_hito2_url' => 'Ver informe hito 2',
  'evidencia_url'     => 'Ver evidencia',
  'acta_pdf_url'      => 'Ver Acta',
];

/* --------- Títulos más “humanos” (opcional) --------- */
$pretty = [
  'estudiante'                 => 'Estudiante',
  'rut'                        => 'RUT',
  'estudiante_email'           => 'Email estudiante',
  'empresa'                    => 'Empresa',
  'practica'                   => 'Práctica',
  'inicio'                     => 'Inicio',
  'fin'                        => 'Fin',
  'informe_hito1_url'          => 'Informe H1',
  'informe_hito1_fecha'        => 'Fecha H1',
  'nota_hito1_interno'         => 'Nota rúbrica H1 (int.)',
  'informe_hito2_url'          => 'Informe H2',
  'informe_hito2_fecha'        => 'Fecha H2',
  'nota_hito2_interno'         => 'Nota rúbrica H2 (int.)',
  'entrevista_fecha'           => 'Entrevista',
  'evidencia_url'              => 'Evidencia',
  'acta_pdf_url'               => 'Acta',
  'supervisor_externo_nombre'  => 'Supervisor externo',
  'supervisor_externo_email'   => 'Email supervisor',
  'supervisor_externo_telefono'=> 'Fono supervisor',
  'supervisor_externo_cargo'   => 'Cargo supervisor',
];

?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Consolidado de Prácticas</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
  body { background:#f7f9fb; }
  .card { border-radius: 14px; }
  .table a { text-decoration: none; }
  .table a:hover { text-decoration: underline; }
  thead th { position: sticky; top: 0; z-index: 1; }
</style>
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Consolidado de Prácticas</h1>
    <span class="text-muted small">
      Rango: <?= htmlspecialchars($ini) ?> → <?= htmlspecialchars($fin) ?> ·
      Registros: <strong><?= count($rows) ?></strong>
    </span>
  </div>

  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form class="row g-3" method="get">
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
          <div class="form-check me-3">
            <input class="form-check-input" type="checkbox" name="solo_cerradas" value="1" id="chkC" <?= $soloCerr ? 'checked':''; ?>>
            <label class="form-check-label" for="chkC">Sólo cerradas</label>
          </div>
          <div class="ms-auto">
            <button class="btn btn-primary me-2" type="submit">Filtrar</button>
            <a class="btn btn-success me-2" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&solo_cerradas=<?=$soloCerr?>&formato=xls">XLS (srv)</a>
            <a class="btn btn-outline-secondary" href="?ini=<?=$ini?>&fin=<?=$fin?>&practica=<?=urlencode($practica)?>&solo_cerradas=<?=$soloCerr?>&formato=csv">CSV (srv)</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table id="tablaConsolidado" class="table table-striped table-hover table-bordered align-middle nowrap" style="width:100%">
          <thead class="table-light">
            <tr>
              <?php if (!empty($rows)):
                foreach (array_keys($rows[0]) as $c):
                  $title = $pretty[$c] ?? $c;
                  echo "<th>".htmlspecialchars($title)."</th>";
                endforeach;
              endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)):
              foreach ($rows as $r):
                echo "<tr>";
                foreach ($r as $k => $v) {
                  if ($k === 'practica') {
                    echo "<td>".badgePractica((string)$v)."</td>";
                  } elseif ($k === 'estudiante_email') {
                    echo "<td>".mailOrDash($v)."</td>";
                  } elseif (isset($linkLabels[$k])) {
                    echo "<td>".renderLinkOrDash($v, $linkLabels[$k])."</td>";
                  } else {
                    echo "<td>".htmlspecialchars((string)$v)."</td>";
                  }
                }
                echo "</tr>";
              endforeach;
            else: ?>
              <tr><td>Sin datos para los filtros seleccionados</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="text-muted small mt-2">* También puedes usar los botones de la tabla (arriba) para copiar o exportar CSV/Excel del lado cliente.</div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
  $(function () {
    $('#tablaConsolidado').DataTable({
      responsive: true,
      pageLength: 25,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'Todos']],
      order: [], // deja el orden como venga del SQL
      dom: 'Bfrtip',
      buttons: [
        'pageLength',
        { extend: 'colvis', text: 'Columnas' },
        { extend: 'copyHtml5', text: 'Copiar' },
        { extend: 'csvHtml5',  text: 'CSV (cli)', title: 'consolidado' },
        { extend: 'excelHtml5',text: 'Excel (cli)', title: 'consolidado' }
      ],
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
      }
    });
  });
</script>
</body>
</html>
