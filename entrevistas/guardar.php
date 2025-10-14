<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método no permitido');
}

/* --------- Recibir y normalizar --------- */
$estudiante_id   = isset($_POST['estudiante_id']) ? (int)$_POST['estudiante_id'] : 0;
$hito_id         = isset($_POST['hito_id'])       ? (int)$_POST['hito_id']       : 0;

/* supervisor puede ser opcional */
$supervisor_id   = isset($_POST['supervisor_id']) && $_POST['supervisor_id'] !== '' 
                   ? (int)$_POST['supervisor_id'] : null;

$fecha           = trim($_POST['fecha'] ?? '');
$modalidad       = trim($_POST['modalidad'] ?? '');     // presencial / online, etc. (opcional)
$tipo_supervisor = trim($_POST['tipo_supervisor'] ?? ''); // interno / externo
/* el formulario puede enviarlo como 'comentarios' o 'comentario' */
$comentarios     = trim($_POST['comentarios'] ?? ($_POST['comentario'] ?? ''));
$evidencia_url   = trim($_POST['evidencia_url'] ?? '');

/* --------- Validaciones básicas --------- */
if ($estudiante_id <= 0 || $hito_id <= 0 || $fecha === '') {
  header('Location: crear.php?msg=Faltan+datos+obligatorios');
  exit;
}

/* fecha válida (YYYY-MM-DD) */
$dt = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$dt || $dt->format('Y-m-d') !== $fecha) {
  header('Location: crear.php?msg=Fecha+inválida');
  exit;
}

/* validar tipo_supervisor */
if ($tipo_supervisor !== '') {
  $tipo_supervisor = strtolower($tipo_supervisor);
  if (!in_array($tipo_supervisor, ['interno','externo'], true)) {
    $tipo_supervisor = null; // lo dejamos nulo y lo inferimos si es posible
  }
}

/* si no vino tipo_supervisor pero sí supervisor, intentar inferirlo */
if (!$tipo_supervisor && $supervisor_id) {
  $q = $pdo->prepare('SELECT tipo FROM supervisores WHERE id = ?');
  $q->execute([$supervisor_id]);
  $tipo_supervisor = $q->fetchColumn() ?: null; // valores esperados: interno/externo
}

/* si evidencia_url viene vacío, guardamos NULL */
$evidencia_url = ($evidencia_url !== '') ? $evidencia_url : null;
/* modalidad opcional: si viene vacío guardamos NULL */
$modalidad     = ($modalidad !== '') ? $modalidad : null;

/* --------- Inserción --------- */
$sql = "INSERT INTO entrevistas
          (estudiante_id, hito_id, supervisor_id, fecha, modalidad, evidencia_url, comentarios, tipo_supervisor)
        VALUES
          (:estudiante_id, :hito_id, :supervisor_id, :fecha, :modalidad, :evidencia_url, :comentarios, :tipo_supervisor)";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':estudiante_id',  $estudiante_id,   PDO::PARAM_INT);
$stmt->bindValue(':hito_id',        $hito_id,         PDO::PARAM_INT);
$supervisor_id === null
  ? $stmt->bindValue(':supervisor_id', null, PDO::PARAM_NULL)
  : $stmt->bindValue(':supervisor_id', $supervisor_id, PDO::PARAM_INT);
$stmt->bindValue(':fecha',          $fecha,           PDO::PARAM_STR);
$modalidad === null
  ? $stmt->bindValue(':modalidad',     null, PDO::PARAM_NULL)
  : $stmt->bindValue(':modalidad',     $modalidad, PDO::PARAM_STR);
$evidencia_url === null
  ? $stmt->bindValue(':evidencia_url', null, PDO::PARAM_NULL)
  : $stmt->bindValue(':evidencia_url', $evidencia_url, PDO::PARAM_STR);
$stmt->bindValue(':comentarios',    $comentarios !== '' ? $comentarios : null, $comentarios !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
$tipo_supervisor === null
  ? $stmt->bindValue(':tipo_supervisor', null, PDO::PARAM_NULL)
  : $stmt->bindValue(':tipo_supervisor', $tipo_supervisor, PDO::PARAM_STR);

try {
  $stmt->execute();
  header('Location: listar.php?ok=entrevista_creada&msg=Entrevista+registrada+correctamente');
  exit;
} catch (PDOException $e) {
  // en producción no expongas el error; aquí dejamos mensaje genérico
  header('Location: crear.php?msg=Error+al+guardar+la+entrevista');
  exit;
}
