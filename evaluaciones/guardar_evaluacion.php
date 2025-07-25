<?php
require_once('../includes/db.php');

// Recoger datos del formulario
$rubrica_id = $_POST['rubrica_id'];
$estudiante_id = $_POST['estudiante_id'];
$hito_id = $_POST['hito_id'];
$supervisor_id = $_POST['supervisor_id'];
$observaciones = $_POST['observaciones'];
$criterios = $_POST['criterios'] ?? [];
$evaluacion_id = $_POST['evaluacion_id'] ?? null;

// Obtener nombre del supervisor
$stmt = $pdo->prepare("SELECT nombre FROM supervisores WHERE id = ?");
$stmt->execute([$supervisor_id]);
$supervisor_nombre = $stmt->fetchColumn();

// Calcular puntaje total
$total = 0;
foreach ($criterios as $criterio_id => $nivel_id) {
    $stmt = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
    $stmt->execute([$criterio_id, $nivel_id]);
    $p = $stmt->fetchColumn();
    $total += $p;
}

if ($evaluacion_id) {
    // Modo edición: actualizar evaluación existente
    $stmt = $pdo->prepare("UPDATE evaluaciones SET estudiante_id = ?, hito_id = ?, supervisor = ?, nota = ?, observaciones = ?, fecha_evaluacion = NOW() WHERE id = ?");
    $stmt->execute([$estudiante_id, $hito_id, $supervisor_nombre, $total, $observaciones, $evaluacion_id]);

    // Eliminar criterios anteriores
    $pdo->prepare("DELETE FROM evaluaciones_criterios WHERE evaluacion_id = ?")->execute([$evaluacion_id]);
} else {
    // Nueva evaluación
    $stmt = $pdo->prepare("INSERT INTO evaluaciones (estudiante_id, hito_id, supervisor, nota, observaciones, fecha_evaluacion) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$estudiante_id, $hito_id, $supervisor_nombre, $total, $observaciones]);
    $evaluacion_id = $pdo->lastInsertId();
}

// Insertar criterios seleccionados
foreach ($criterios as $criterio_id => $nivel_id) {
    $stmt = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
    $stmt->execute([$criterio_id, $nivel_id]);
    $puntaje = $stmt->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO evaluaciones_criterios (evaluacion_id, criterio_id, nivel_logro_id, puntaje_obtenido) VALUES (?, ?, ?, ?)");
    $stmt->execute([$evaluacion_id, $criterio_id, $nivel_id, $puntaje]);
}

header("Location: listar.php");
exit;
?>