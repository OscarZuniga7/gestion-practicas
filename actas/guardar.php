<?php
// actas/guardar.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../entrevistas/listar.php');
  exit;
}

/* ============
   1) INPUTS
   ============ */
$acta_id        = isset($_POST['acta_id']) ? (int)$_POST['acta_id'] : 0;   // solo en editar
$entrevista_id  = isset($_POST['entrevista_id']) ? (int)$_POST['entrevista_id'] : 0;

$tipo_entrevista = $_POST['tipo_entrevista'] ?? null;          // 'intermedia' | 'final'
$link_vc         = trim((string)($_POST['link_vc'] ?? ''));
$acta_pdf_url = trim((string)($_POST['acta_pdf_url'] ?? ''));
$fecha_firma     = $_POST['fecha_firma'] ?? '';                 // 'YYYY-MM-DD' | '' | null
$firmar_hoy      = isset($_POST['firmar_hoy']) ? 1 : 0;         // opcional en tus formularios

$eval_general = trim((string)($_POST['eval_general'] ?? ''));
$fortalezas   = trim((string)($_POST['fortalezas']   ?? ''));
$mejoras      = trim((string)($_POST['mejoras']      ?? ''));
$sugerencias  = trim((string)($_POST['sugerencias']  ?? ''));

$recibi_practicantes = $_POST['recibi_practicantes'] ?? null;   // 'si' | 'no' | 'tal_vez'
$recibi_obs          = trim((string)($_POST['recibi_obs']       ?? null));
$convenio            = $_POST['convenio']            ?? null;   // 'si' | 'no' | 'tal_vez'
$convenio_obs        = trim((string)($_POST['convenio_obs']     ?? null));
$otras_vinc          = $_POST['otras_vinc']          ?? null;   // 'si' | 'no' | 'tal_vez'
$otras_vinc_det      = trim((string)($_POST['otras_vinc_det']   ?? null));
$obs_docente         = trim((string)($_POST['obs_docente']      ?? ''));

// Validación mínima
if (!in_array($tipo_entrevista, ['intermedia','final'], true)) {
  $redir = $acta_id ? "editar.php?id={$acta_id}" : "crear.php?entrevista_id={$entrevista_id}";
  header("Location: {$redir}&err=tipo");
  exit;
}
if ($acta_id <= 0 && $entrevista_id <= 0) {
  header('Location: ../entrevistas/listar.php?msg=acta_sin_ids');
  exit;
}

// Normalizar fecha_firma (si marcaste 'firmar_hoy', domina al input de fecha)
if ($firmar_hoy === 1) {
  $fecha_firma_norm = date('Y-m-d');
} else {
  $fecha_firma_norm = ($fecha_firma === '' ? null : $fecha_firma);
}

// Si es intermedia, limpiar campos de vinculación
if ($tipo_entrevista === 'intermedia') {
  $recibi_practicantes = null;
  $recibi_obs          = null;
  $convenio            = null;
  $convenio_obs        = null;
  $otras_vinc          = null;
  $otras_vinc_det      = null;
}

/* ============
   2) ACCIÓN
   ============ */
try {
  $pdo->beginTransaction();

  if ($acta_id > 0) {
    // --- UPDATE ---
    // Confirmar que exista
    $chk = $pdo->prepare("SELECT id, entrevista_id FROM actas_entrevista WHERE id=?");
    $chk->execute([$acta_id]);
    $row = $chk->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
      $pdo->rollBack();
      header('Location: ../entrevistas/listar.php?msg=acta_no_existe');
      exit;
    }

    $sql = "UPDATE actas_entrevista
               SET tipo_entrevista = ?,
                   link_vc         = ?,
                   acta_pdf_url    = ?,
                   fecha_firma     = ?,
                   eval_general    = ?,
                   fortalezas      = ?,
                   mejoras         = ?,
                   sugerencias     = ?,
                   recibi_practicantes = ?,
                   recibi_obs      = ?,
                   convenio        = ?,
                   convenio_obs    = ?,
                   otras_vinc      = ?,
                   otras_vinc_det  = ?,
                   obs_docente     = ?
             WHERE id = ?";
    $pdo->prepare($sql)->execute([
      $tipo_entrevista,
      ($link_vc !== '' ? $link_vc : null),
      ($acta_pdf_url !== '' ? $acta_pdf_url : null),
      $fecha_firma_norm,
      $eval_general, $fortalezas, $mejoras, $sugerencias,
      $recibi_practicantes, $recibi_obs, $convenio, $convenio_obs, $otras_vinc, $otras_vinc_det,
      $obs_docente,
      $acta_id
    ]);

    $pdo->commit();
    header('Location: ../entrevistas/listar.php?ok=acta_actualizada');
    exit;

  } else {
    // --- INSERT ---
    // Regla 1: una sola acta por entrevista.
    $dup = $pdo->prepare("SELECT id FROM actas_entrevista WHERE entrevista_id=?");
    $dup->execute([$entrevista_id]);
    if ($dup->fetchColumn()) {
      $pdo->rollBack();
      // Redirigir al editor del acta existente
      $exist_id = $pdo->query("SELECT id FROM actas_entrevista WHERE entrevista_id={$entrevista_id} LIMIT 1")->fetchColumn();
      header("Location: editar.php?id=".(int)$exist_id."&msg=ya_existe");
      exit;
    }

    // Confirmar entrevista válida
    $chkE = $pdo->prepare("SELECT id FROM entrevistas WHERE id=?");
    $chkE->execute([$entrevista_id]);
    if (!$chkE->fetchColumn()) {
      $pdo->rollBack();
      header('Location: ../entrevistas/listar.php?msg=entrevista_invalida');
      exit;
    }

    $sql = "INSERT INTO actas_entrevista
              (entrevista_id, tipo_entrevista, link_vc, acta_pdf_url, fecha_firma,
               eval_general, fortalezas, mejoras, sugerencias,
               recibi_practicantes, recibi_obs, convenio, convenio_obs, otras_vinc, otras_vinc_det,
               obs_docente)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $pdo->prepare($sql)->execute([
      $entrevista_id, $tipo_entrevista,
      ($link_vc !== '' ? $link_vc : null),
      ($acta_pdf_url !== '' ? $acta_pdf_url : null),
      $fecha_firma_norm,
      $eval_general, $fortalezas, $mejoras, $sugerencias,
      $recibi_practicantes, $recibi_obs, $convenio, $convenio_obs, $otras_vinc, $otras_vinc_det,
      $obs_docente
    ]);

    $pdo->commit();
    header('Location: ../entrevistas/listar.php?ok=acta_creada');
    exit;
  }

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  // En producción conviene loguear $e->getMessage()
  $redir = $acta_id ? "editar.php?id={$acta_id}" : "crear.php?entrevista_id={$entrevista_id}";
  header("Location: {$redir}&err=save");
  exit;
}
