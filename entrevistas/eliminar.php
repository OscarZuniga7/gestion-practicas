<?php
// entrevistas/eliminar.php
require_once __DIR__ . '/../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header('Location: listar.php?msg=ID+de+entrevista+inválido');
  exit;
}

// ¿Existe la entrevista?
$st = $pdo->prepare('SELECT id FROM entrevistas WHERE id = ?');
$st->execute([$id]);
if (!$st->fetchColumn()) {
  header('Location: listar.php?msg=La+entrevista+no+existe+o+ya+fue+eliminada');
  exit;
}

// ¿Tiene acta asociada?
$st = $pdo->prepare('SELECT id, acta_pdf_url FROM actas_entrevista WHERE entrevista_id = ? LIMIT 1');
$st->execute([$id]);
$acta = $st->fetch(PDO::FETCH_ASSOC);
$tieneActa = (bool)$acta;

// (Opcional) Si quieres impedir borrar cuando hay acta, descomenta esto:
/*
if ($tieneActa) {
  header('Location: listar.php?msg=No+puede+eliminar:+la+entrevista+tiene+un+acta+asociada');
  exit;
}
*/

try {
  $pdo->beginTransaction();

  // Si NO tienes ON DELETE CASCADE y quieres borrar el acta manualmente:
  // if ($tieneActa) {
  //   $delA = $pdo->prepare('DELETE FROM actas_entrevista WHERE entrevista_id = ?');
  //   $delA->execute([$id]);
  // }

  // Borrar entrevista (si tienes FK ON DELETE CASCADE, el acta se elimina sola)
  $del = $pdo->prepare('DELETE FROM entrevistas WHERE id = ?');
  $del->execute([$id]);

  $pdo->commit();

  // (Opcional) eliminar archivo físico del acta, si guardaste una ruta local.
  // Requiere que acta_pdf_url sea un path interno. Hazlo con MUCHO cuidado.
  /*
  if ($tieneActa && !empty($acta['acta_pdf_url'])) {
      $path = $_SERVER['DOCUMENT_ROOT'] . $acta['acta_pdf_url'];
      if (is_file($path)) { @unlink($path); }
  }
  */

  $msg = $tieneActa
    ? 'Entrevista+y+su+acta+fueron+eliminadas'
    : 'Entrevista+eliminada+correctamente';

  header('Location: listar.php?ok=entrevista_eliminada&msg=' . $msg);
  exit;

} catch (PDOException $e) {
  if ($pdo->inTransaction()) { $pdo->rollBack(); }
  // No exponemos el error exacto en producción
  header('Location: listar.php?msg=Error+al+eliminar+la+entrevista');
  exit;
}
