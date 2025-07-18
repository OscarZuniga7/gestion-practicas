<?php
require_once('../includes/db.php');

// Validar campos obligatorios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id     = $_POST['estudiante_id'];
    $hito_id           = $_POST['hito_id'];
    $supervisor_id     = $_POST['supervisor_id'];
    $rubrica_id        = $_POST['rubrica_id'];
    $observaciones     = $_POST['observaciones'] ?? '';
    $criterios         = $_POST['criterios'] ?? [];

    // Obtener nombre del supervisor
    $stmt = $pdo->prepare("SELECT nombre FROM supervisores WHERE id = ?");
    $stmt->execute([$supervisor_id]);
    $supervisor_nombre = $stmt->fetchColumn();

    // Calcular fecha actual y total de puntaje
    $fecha_evaluacion = date('Y-m-d');
    $total_puntaje = 0;

    // Insertar en tabla evaluaciones
    $stmt = $pdo->prepare("INSERT INTO evaluaciones 
        (estudiante_id, hito_id, supervisor, nota, fecha_evaluacion, observaciones, archivo)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Insertamos con nota = 0 temporalmente, luego se actualizará
    $stmt->execute([
        $estudiante_id,
        $hito_id,
        $supervisor_nombre,
        0, // nota temporal
        $fecha_evaluacion,
        $observaciones,
        null // no hay archivo desde este formulario
    ]);

    // Obtener ID generado
    $evaluacion_id = $pdo->lastInsertId();

    // Guardar cada criterio evaluado
    foreach ($criterios as $criterio_id => $nivel_logro_id) {
        // Obtener puntaje desde criterios_niveles
        $stmt = $pdo->prepare("SELECT puntaje FROM criterios_niveles WHERE criterio_id = ? AND nivel_logro_id = ?");
        $stmt->execute([$criterio_id, $nivel_logro_id]);
        $puntaje = $stmt->fetchColumn();

        if ($puntaje === false) continue; // seguridad

        // Insertar en evaluaciones_criterios
        $stmt = $pdo->prepare("INSERT INTO evaluaciones_criterios
            (evaluacion_id, criterio_id, nivel_logro_id, puntaje_obtenido)
            VALUES (?, ?, ?, ?)");

        $stmt->execute([$evaluacion_id, $criterio_id, $nivel_logro_id, $puntaje]);

        // Sumar al total
        $total_puntaje += $puntaje;
    }

    // Actualizar la nota final en la tabla evaluaciones
    $stmt = $pdo->prepare("UPDATE evaluaciones SET nota = ? WHERE id = ?");
    $stmt->execute([$total_puntaje, $evaluacion_id]);

    // Redirigir al listado
    header("Location: listar.php");
    exit;
} else {
    echo "Acceso no permitido.";
}
