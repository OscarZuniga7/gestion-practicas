<?php
// actas/guardar.php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../entrevistas/listar.php');
    exit;
}

$entrevista_id   = isset($_POST['entrevista_id']) ? (int) $_POST['entrevista_id'] : 0;
$tipo_entrevista = $_POST['tipo_entrevista'] ?? null;            // intermedia | final
$link_vc         = trim($_POST['link_vc'] ?? '');
$fecha_firma     = $_POST['fecha_firma'] ?? null;

$eval_general = trim($_POST['eval_general'] ?? '');
$fortalezas   = trim($_POST['fortalezas'] ?? '');
$mejoras      = trim($_POST['mejoras'] ?? '');
$sugerencias  = trim($_POST['sugerencias'] ?? '');

$recibi_practicantes = $_POST['recibi_practicantes'] ?? null;     // si | no | tal_vez (solo final)
$recibi_obs          = trim($_POST['recibi_obs'] ?? null);
$convenio            = $_POST['convenio'] ?? null;                // si | no | tal_vez
$convenio_obs        = trim($_POST['convenio_obs'] ?? null);
$otras_vinc          = $_POST['otras_vinc'] ?? null;              // si | no | tal_vez
$otras_vinc_det      = trim($_POST['otras_vinc_det'] ?? null);

$obs_docente         = trim($_POST['obs_docente'] ?? '');

// Validación mínima
if ($entrevista_id <= 0 || !in_array($tipo_entrevista, ['intermedia','final'], true)) {
    header('Location: crear.php?entrevista_id='.(int)$entrevista_id.'&err=1');
    exit;
}

// Evitar duplicado (1 acta por entrevista)
$existe = $pdo->prepare("SELECT id FROM actas_entrevista WHERE entrevista_id = ?");
$existe->execute([$entrevista_id]);
if ($existe->fetch()) {
    // ya existe: redirigir a editar
    header('Location: editar.php?entrevista_id='.(int)$entrevista_id.'&msg=ya_existe');
    exit;
}

// Si es intermedia, limpiar campos de vinculación
if ($tipo_entrevista === 'intermedia') {
    $recibi_practicantes = $recibi_obs = $convenio = $convenio_obs = $otras_vinc = $otras_vinc_det = null;
}

// Normalizar fecha
if ($fecha_firma === '') $fecha_firma = null;

// Insert
$sql = "INSERT INTO actas_entrevista
(entrevista_id, tipo_entrevista, link_vc, fecha_firma,
 eval_general, fortalezas, mejoras, sugerencias,
 recibi_practicantes, recibi_obs, convenio, convenio_obs, otras_vinc, otras_vinc_det,
 obs_docente)
VALUES
(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $entrevista_id, $tipo_entrevista, $link_vc, $fecha_firma,
    $eval_general, $fortalezas, $mejoras, $sugerencias,
    $recibi_practicantes, $recibi_obs, $convenio, $convenio_obs, $otras_vinc, $otras_vinc_det,
    $obs_docente
]);

header('Location: ../entrevistas/listar.php?ok=acta_creada');
exit;
