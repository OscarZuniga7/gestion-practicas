<?php
// evaluaciones/guardar_detalle_url.php
declare(strict_types=1);
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: listar.php'); exit;
}

$evalId = isset($_POST['evaluacion_id']) ? (int)$_POST['evaluacion_id'] : 0;
$url    = trim((string)($_POST['detalle_pdf_url'] ?? ''));

// Validaciones básicas
if ($evalId <= 0) { header('Location: listar.php?err=bad_id'); exit; }
if ($url === '')  { header('Location: listar.php?err=empty'); exit; }
if (!filter_var($url, FILTER_VALIDATE_URL)) {
  header('Location: listar.php?err=bad_url'); exit;
}
$parts = parse_url($url);
if (!isset($parts['scheme']) || strtolower($parts['scheme']) !== 'https') {
  header('Location: listar.php?err=scheme'); exit;
}

// (Opcional) restringir a dominios de confianza
$allowed = ['sharepoint.com','1drv.ms','onedrive.live.com'];
$host = strtolower($parts['host'] ?? '');
$okHost = false;
foreach ($allowed as $dom) {
  // termina en dominio permitido
  if (substr($host, -strlen($dom)) === $dom) { $okHost = true; break; }
}
if (!$okHost) {
  // comenta este bloque si no quieres restringir por dominio
  header('Location: listar.php?err=host'); exit;
}

// Guardar
$stmt = $pdo->prepare("UPDATE evaluaciones SET detalle_pdf_url = ? WHERE id = ?");
$stmt->execute([$url, $evalId]);

header('Location: listar.php?ok=detalle_url');
exit;
