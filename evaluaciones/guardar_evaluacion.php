<?php
// evaluaciones/guardar_evaluacion.php
declare(strict_types=1);

require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: listar.php');
  exit;
}

/* ============================
   1) ENTRADAS Y VALIDACIONES
   ============================ */
$rubrica_id     = isset($_POST['rubrica_id'])     ? (int)$_POST['rubrica_id']     : 0;
$estudiante_id  = isset($_POST['estudiante_id'])  ? (int)$_POST['estudiante_id']  : 0;
$hito_id        = isset($_POST['hito_id'])        ? (int)$_POST['hito_id']        : 0;
$supervisor_id  = isset($_POST['supervisor_id'])  ? (int)$_POST['supervisor_id']  : 0;
$observaciones  = trim((string)($_POST['observaciones'] ?? ''));
$criterios      = $_POST['criterios'] ?? []; // array: criterio_id => nivel_logro_id
$evaluacion_id  = isset($_POST['evaluacion_id'])  ? (int)$_POST['evaluacion_id']  : 0;

if ($rubrica_id <= 0 || $estudiante_id <= 0 || $hito_id <= 0 || $supervisor_id <= 0) {
  header('Location: evaluacion_rubrica.php?err=inputs');
  exit;
}
if (empty($criterios) || !is_array($criterios)) {
  header("Location: evaluacion_rubrica.php?rubrica_id={$rubrica_id}&estudiante_id={$estudiante_id}&hito_id={$hito_id}&err=sin_criterios");
  exit;
}

/* ============================
   2) CONSISTENCIA DE DATOS
   ============================ */
// (a) El supervisor existe
$st = $pdo->prepare("SELECT nombre FROM supervisores WHERE id = ?");
$st->execute([$supervisor_id]);
$supervisor_nombre = (string)$st->fetchColumn();
if ($supervisor_nombre === '') {
  header("Location: evaluacion_rubrica.php?rubrica_id={$rubrica_id}&estudiante_id={$estudiante_id}&hito_id={$hito_id}&err=supervisor_invalido");
  exit;
}

// (b) Verificar que los criterios recibidos pertenecen a la rúbrica seleccionada
$criterio_ids = array_map('intval', array_keys($criterios));
$placeholders = implode(',', array_fill(0, count($criterio_ids), '?'));
$sqlCheck = "SELECT id FROM criterios WHERE rubrica_id = ? AND id IN ($placeholders)";
$st = $pdo->prepare($sqlCheck);
$st->execute(array_merge([$rubrica_id], $criterio_ids));
$ids_validos = $st->fetchAll(PDO::FETCH_COLUMN, 0);

if (count($ids_validos) !== count($criterio_ids)) {
  header("Location: evaluacion_rubrica.php?rubrica_id={$rubrica_id}&estudiante_id={$estudiante_id}&hito_id={$hito_id}&err=criterios_incongruentes");
  exit;
}

/* ============================
   3) CALCULAR PUNTAJE TOTAL
   ============================ */
$total = 0;
$puntajes_por_criterio = []; // para no hacer dos consultas luego
$stP = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
foreach ($criterios as $criterio_id => $nivel_id) {
  $cid = (int)$criterio_id;
  $nid = (int)$nivel_id;
  $stP->execute([$cid, $nid]);
  $puntaje = $stP->fetchColumn();
  $puntaje = is_numeric($puntaje) ? (int)$puntaje : 0;
  $puntajes_por_criterio[$cid] = $puntaje;
  $total += $puntaje;
}

/* ============================
   4) INSERT / UPDATE (TX)
   ============================ */
try {
  $pdo->beginTransaction();

  // Si es UPDATE, confirmar existencia y limpiar detalle
  if ($evaluacion_id > 0) {
    $chk = $pdo->prepare("SELECT id FROM evaluaciones WHERE id = ?");
    $chk->execute([$evaluacion_id]);
    if (!$chk->fetchColumn()) {
      $pdo->rollBack();
      header('Location: listar.php?msg=evaluacion_no_existe');
      exit;
    }
    // eliminamos el detalle anterior
    $pdo->prepare("DELETE FROM evaluaciones_criterios WHERE evaluacion_id = ?")->execute([$evaluacion_id]);

    // Intento principal: esquema nuevo con supervisor_id
    $ok = false;
    try {
      $sqlU = "UPDATE evaluaciones
                  SET estudiante_id = ?, hito_id = ?, supervisor_id = ?, nota = ?, observaciones = ?, fecha_evaluacion = NOW()
                WHERE id = ?";
      $pdo->prepare($sqlU)->execute([$estudiante_id, $hito_id, $supervisor_id, $total, $observaciones, $evaluacion_id]);
      $ok = true;
    } catch (PDOException $e) {
      // Fallback si la columna supervisor_id no existe (esquema antiguo)
      $sqlU2 = "UPDATE evaluaciones
                   SET estudiante_id = ?, hito_id = ?, supervisor = ?, nota = ?, observaciones = ?, fecha_evaluacion = NOW()
                 WHERE id = ?";
      $pdo->prepare($sqlU2)->execute([$estudiante_id, $hito_id, $supervisor_nombre, $total, $observaciones, $evaluacion_id]);
      $ok = true;
    }

  } else {
    // INSERT
    $ok = false;
    try {
      // Intento principal: esquema nuevo con supervisor_id
      $sqlI = "INSERT INTO evaluaciones (estudiante_id, hito_id, supervisor_id, nota, observaciones, fecha_evaluacion)
               VALUES (?, ?, ?, ?, ?, NOW())";
      $pdo->prepare($sqlI)->execute([$estudiante_id, $hito_id, $supervisor_id, $total, $observaciones]);
      $evaluacion_id = (int)$pdo->lastInsertId();
      $ok = true;
    } catch (PDOException $e) {
      // Fallback si no existe supervisor_id: usar columna antigua 'supervisor' (texto)
      $sqlI2 = "INSERT INTO evaluaciones (estudiante_id, hito_id, supervisor, nota, observaciones, fecha_evaluacion)
                VALUES (?, ?, ?, ?, ?, NOW())";
      $pdo->prepare($sqlI2)->execute([$estudiante_id, $hito_id, $supervisor_nombre, $total, $observaciones]);
      $evaluacion_id = (int)$pdo->lastInsertId();
      $ok = true;
    }
  }

  if (!$ok) {
    throw new RuntimeException('No fue posible guardar la evaluación.');
  }

  // Insertar detalle evaluaciones_criterios
  $ins = $pdo->prepare("
    INSERT INTO evaluaciones_criterios (evaluacion_id, criterio_id, nivel_logro_id, puntaje_obtenido)
    VALUES (?, ?, ?, ?)
  ");
  foreach ($criterios as $criterio_id => $nivel_id) {
    $puntaje = (int)($puntajes_por_criterio[(int)$criterio_id] ?? 0);
    $ins->execute([$evaluacion_id, (int)$criterio_id, (int)$nivel_id, $puntaje]);
  }

  $pdo->commit();

  // Redirigir de vuelta a la lista (puedes llevar a ver/editar si prefieres)
  header('Location: listar.php?ok=eval_guardada');
  exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  // En producción conviene loguear $e->getMessage()
  $params = http_build_query([
    'rubrica_id'    => $rubrica_id,
    'estudiante_id' => $estudiante_id,
    'hito_id'       => $hito_id,
    'supervisor_id' => $supervisor_id,
    'err'           => 'save'
  ]);
  header("Location: evaluacion_rubrica.php?{$params}");
  exit;
}
