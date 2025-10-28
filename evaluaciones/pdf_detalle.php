<?php
// evaluaciones/pdf_detalle.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

// ---------- Parámetros ----------
$evaluacion_id = isset($_GET['evaluacion_id']) ? (int)$_GET['evaluacion_id'] : 0;
if ($evaluacion_id <= 0) {
  http_response_code(400);
  exit('Falta evaluacion_id');
}

// ---------- Traer datos desde la vista detalle ----------
$sql = "
  SELECT
    evaluacion_id,
    evaluacion_fecha,
    estudiante_id,
    estudiante,
    estudiante_email,
    empresa,
    practica,
    hito_id,
    hito,
    evaluador_id,
    evaluador_nombre,
    rubrica_id,
    rubrica_nombre,
    criterio_orden,
    criterio_nombre,
    nivel_logro_id,
    nivel_nombre,
    puntaje_obtenido,
    puntaje_max,
    observaciones_generales
  FROM vw_rubrica_evaluacion_detalle
  WHERE evaluacion_id = ?
  ORDER BY criterio_orden ASC, criterio_nombre ASC
";
$st = $pdo->prepare($sql);
$st->execute([$evaluacion_id]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
  http_response_code(404);
  exit('No hay datos para esta evaluación.');
}

$head = $rows[0];

// Detecta Práctica II (ajusta si usas otra codificación)
$txtPractica  = strtoupper((string)$head['practica']);
$esPracticaII = (strpos($txtPractica, 'II') !== false);

// --------- Totales base (no ponderados) ---------
$sum_obtenido = 0.0;   // suma de puntajes logrados (sin ponderar)
$sum_max      = 0.0;   // para P1 = suma máximos (pts); para P2 = suma de porcentajes (debería ≈ 100)

foreach ($rows as $r) {
  $sum_obtenido += (float)$r['puntaje_obtenido'];
  $sum_max      += (float)$r['puntaje_max'];
}

// --------- Cálculo ponderado para Práctica II ---------
// Necesitamos el puntaje máximo posible por criterio para calcular el máximo ponderado.
// Lo obtenemos por rúbrica y orden de criterio (más robusto que por nombre).
$sum_ponderado      = 0.0;  // Σ (po × % / 100)
$max_ponderado      = 0.0;  // Σ (po_max_del_criterio × % / 100)
$maxPorOrden = [];          // [orden] => max_po_del_criterio

if ($esPracticaII) {
  $q = $pdo->prepare("
    SELECT c.orden, MAX(cn.puntaje) AS max_po
    FROM criterios c
    JOIN criterios_niveles cn ON cn.criterio_id = c.id
    WHERE c.rubrica_id = ?
    GROUP BY c.orden
  ");
  $q->execute([(int)$head['rubrica_id']]);
  while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
    $maxPorOrden[(int)$r['orden']] = (float)$r['max_po'];
  }

  foreach ($rows as $r) {
    $po = (float)$r['puntaje_obtenido']; // puntos logrados en el nivel elegido
    $pm = (float)$r['puntaje_max'];      // porcentaje del criterio (0..100) para P2
    $ord = (int)$r['criterio_orden'];
    $po_max = $maxPorOrden[$ord] ?? $po; // fallback defensivo

    $sum_ponderado += $po * $pm / 100.0;
    $max_ponderado += $po_max * $pm / 100.0;
  }
}

// --------- Porcentajes y nota ---------
if ($esPracticaII) {
  // porcentaje ponderado respecto al máximo ponderado
  $porc = ($max_ponderado > 0) ? ($sum_ponderado / $max_ponderado) : 0.0;
} else {
  // Práctica I: porcentaje simple
  $porc = ($sum_max > 0) ? ($sum_obtenido / $sum_max) : 0.0;
}

// Nota sugerida (escala 1–7) SOLO si es Práctica II (ajústalo a tu reglamento)
$NOTA_MIN = 1.0;
$NOTA_MAX = 7.0;
$nota_sugerida = $NOTA_MIN + ($NOTA_MAX - $NOTA_MIN) * $porc;
$nota_sugerida = round($nota_sugerida, 1);

// Formateos comunes
$fecha_eval       = $head['evaluacion_fecha'] ? date('d-m-Y', strtotime((string)$head['evaluacion_fecha'])) : '—';
$sum_obtenido_txt = number_format($sum_obtenido, 0, ',', '.');

if ($esPracticaII) {
  $sum_max_txt       = number_format($sum_max, 1, ',', '.') . '%';          // suma de porcentajes
  $sum_pond_txt      = number_format($sum_ponderado, 1, ',', '.');          // total ponderado logrado
  $max_pond_txt      = number_format($max_ponderado, 1, ',', '.');          // máximo ponderado posible
  $porc_txt          = number_format($porc * 100, 1, ',', '.') . '%';       // porcentaje ponderado
} else {
  $sum_max_txt = number_format($sum_max, 0, ',', '.');                       // máximo en puntos
  $porc_txt    = number_format($porc * 100, 1, ',', '.') . '%';
}

// Logo (opcional, mismo que usaste en actas/pdf.php)
$logoPath = __DIR__ . '/../public/img/unab_logo.png';
$logoSrc = (file_exists($logoPath))
  ? ('data:image/png;base64,' . base64_encode(file_get_contents($logoPath)))
  : '';

// Construcción HTML
ob_start();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Detalle Rúbrica - <?= htmlspecialchars($head['estudiante']) ?></title>
<style>
  *{ box-sizing: border-box; }
  body{ font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; margin: 24px; }
  h1{ font-size: 16pt; margin: 0 0 6px; text-transform: uppercase; }
  h2{ font-size: 13pt; margin: 18px 0 8px; border-bottom:1px solid #ccc; padding-bottom:4px;}
  .box{ border:1px solid #999; padding:10px; border-radius:6px; }
  .kv{ display:flex; margin:2px 0; }
  .label{ font-weight:600; width: 220px; }
  .mono{ font-family: ui-monospace, Consolas, Menlo, monospace; }
  table{ width: 100%; border-collapse: collapse; margin-top: 8px; }
  th, td{ border:1px solid #aaa; padding:6px 8px; }
  th{ background:#f3f3f3; }
  .right{ text-align:right; }
  .center{ text-align:center; }

  /* Banner estilo Word */
  .banner { width: 100%; border: 1px solid #0f2c3b; border-collapse: collapse; }
  .banner td { vertical-align: middle; padding: 0; }
  .brand-cell { width: 92px; background: #ffffff; border-right: 0; }
  .brand-box { border-left: 6px solid #c62e2e; padding: 8px 6px; height: 78px; display: flex; align-items: center; justify-content: center; }
  .brand-box img { max-height: 64px; width: auto; display: block; }
  .title-cell { background: #1f4458; color: #ffffff; padding: 10px 16px; }
  .title-top { font-weight: 700; font-size: 14px; text-align: center; letter-spacing: .3px; }
  .title-sub { margin-top: 6px; font-weight: 700; font-size: 11.5px; text-align: center; letter-spacing: .3px; line-height: 1.35; }

  @media print { a#printBtn{ display:none; } body{ margin: 0.6cm; } }
  @page { margin: 18mm 16mm 18mm; }
</style>
</head>
<body>

<table class="banner">
  <tr>
    <td class="brand-cell">
      <div class="brand-box">
        <?php if ($logoSrc): ?><img src="<?= $logoSrc ?>" alt="Universidad Andrés Bello"><?php endif; ?>
      </div>
    </td>
    <td class="title-cell">
      <div class="title-top">DETALLE DE EVALUACIÓN POR RÚBRICA</div>
      <div class="title-sub">FACULTAD DE INGENIERÍA — UNIVERSIDAD ANDRÉS BELLO</div>
    </td>
  </tr>
</table>

<!--<a id="printBtn" href="?evaluacion_id=<?= (int)$evaluacion_id ?>&download=1" style="float:right;margin:8px 0 10px 10px;padding:6px 10px;border:1px solid #999;border-radius:6px;text-decoration:none;color:#111;">Descargar PDF</a>-->

<h2>Datos Generales</h2>
<div class="box">
  <div class="kv"><div class="label">Estudiante</div><div><?= htmlspecialchars($head['estudiante']) ?></div></div>
  <div class="kv"><div class="label">Email</div><div class="mono"><?= htmlspecialchars($head['estudiante_email'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Empresa</div><div><?= htmlspecialchars($head['empresa'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Práctica</div><div><?= htmlspecialchars($head['practica'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Hito</div><div><?= htmlspecialchars($head['hito'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Rúbrica</div><div><?= htmlspecialchars($head['rubrica_nombre'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Evaluador(a)</div><div><?= htmlspecialchars($head['evaluador_nombre'] ?: '—') ?></div></div>
  <div class="kv"><div class="label">Fecha evaluación</div><div><?= $fecha_eval ?></div></div>
</div>

<h2>Detalle por Criterio</h2>

<table>
  <thead>
    <tr>
      <th class="center" style="width:60px;">#</th>
      <th>Criterio</th>
      <th style="width:30%;">Nivel logrado</th>
      <th class="right" style="width:120px;">Puntaje</th>
      <th class="right" style="width:120px;">Máximo<?= $esPracticaII ? ' (%)' : '' ?></th>
      <?php if ($esPracticaII): ?>
        <th class="right" style="width:120px;">Puntaje logrado</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
  <?php
    $suma_pm = 0.0; // para mostrar suma de porcentajes (P2) o suma de máximos (P1)
    foreach ($rows as $r):
      $po = (float)$r['puntaje_obtenido'];
      $pm = (float)$r['puntaje_max'];
      $suma_pm += $pm;

      // P2: puntaje ponderado por %máximo
      $pl = $esPracticaII ? ($po * $pm / 100.0) : null;
  ?>
    <tr>
      <td class="center"><?= (int)$r['criterio_orden'] ?></td>
      <td><?= htmlspecialchars($r['criterio_nombre']) ?></td>
      <td><?= htmlspecialchars($r['nivel_nombre'] ?: '—') ?></td>
      <td class="right"><?= number_format($po, 0, ',', '.') ?></td>
      <td class="right">
        <?= $esPracticaII
            ? (number_format($pm, 1, ',', '.') . '%')
            : number_format($pm, 0, ',', '.') ?>
      </td>
      <?php if ($esPracticaII): ?>
        <td class="right"><?= number_format($pl, 1, ',', '.') ?></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>

  <tfoot>
    <tr>
      <th colspan="3" class="right">Totales</th>
      <th class="right"><?= $sum_obtenido_txt ?></th>
      <th class="right">
        <?= $esPracticaII
            ? (number_format($suma_pm, 1, ',', '.') . '%')
            : $sum_max_txt ?>
      </th>
      <?php if ($esPracticaII): ?>
        <th class="right"><?= $sum_pond_txt ?></th>
      <?php endif; ?>
    </tr>
  </tfoot>
</table>

<h2>Resultado global</h2>
<div class="box">
  <?php if ($esPracticaII): ?>
    <div class="kv"><div class="label">Puntaje ponderado</div><div><?= $sum_pond_txt ?> / <?= $max_pond_txt ?></div></div>
    <div class="kv"><div class="label">Porcentaje (ponderado)</div><div><?= $porc_txt ?></div></div>
    <div class="kv"><div class="label">Nota rúbrica (escala 1–7)</div><div><strong><?= number_format($nota_sugerida, 1, ',', '.') ?></strong></div></div>
    <div class="kv"><div class="label"></div>
      <div class="small" style="color:#555;">
        <em>Calculada como <?= $NOTA_MIN ?> + (<?= $NOTA_MAX - $NOTA_MIN ?> × porcentaje ponderado).</em>
      </div>
    </div>
  <?php else: ?>
    <div class="kv"><div class="label">Puntaje total</div><div><?= $sum_obtenido_txt ?> / <?= $sum_max_txt ?></div></div>
    <div class="kv"><div class="label">Porcentaje</div><div><?= $porc_txt ?></div></div>
  <?php endif; ?>
</div>

<h2>Observaciones del evaluador</h2>
<div class="box">
  <?= nl2br(htmlspecialchars($head['observaciones_generales'] ?: '')) ?>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// ---------- ¿Descargar PDF? ----------
$wantDownload = isset($_GET['download']) && $_GET['download'] == '1';

$dompdfAvailable = false;
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
  require_once $autoload;
  $dompdfAvailable = class_exists(\Dompdf\Dompdf::class);
}

if ($wantDownload && $dompdfAvailable) {
  $dompdf = new \Dompdf\Dompdf([
    'isRemoteEnabled' => true,
    'defaultFont'     => 'dejavu sans'
  ]);
  $dompdf->loadHtml($html, 'UTF-8');
  $dompdf->setPaper('A4', 'portrait');
  $dompdf->render();

  // Nombre de archivo
  $alumnoSlug = preg_replace('/[^a-z0-9]+/i','-', (string)$head['estudiante']);
  $empSlug    = preg_replace('/[^a-z0-9]+/i','-', (string)($head['empresa'] ?? 'empresa'));
  $hitoSlug   = preg_replace('/[^a-z0-9]+/i','-', (string)$head['hito']);
  $fechaYmd   = $head['evaluacion_fecha'] ? date('Y-m-d', strtotime((string)$head['evaluacion_fecha'])) : date('Y-m-d');
  $filename   = "Detalle_Rubrica_{$alumnoSlug}_{$empSlug}_{$hitoSlug}_{$fechaYmd}.pdf";

  $dompdf->stream($filename, ['Attachment' => true]);
  exit;
}

// Fallback: ver HTML
echo $html;
