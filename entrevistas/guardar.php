<?php
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $estudiante_id = $_POST['estudiante_id'];
    $hito_id = $_POST['hito_id'];
    $supervisor_id = $_POST['supervisor_id'];
    $fecha = $_POST['fecha'];
    $comentario = $_POST['comentario'] ?? '';
    $evidencia_url = $_POST['evidencia_url'] ?? '';
    $tipo_supervisor = $_POST['tipo_supervisor'];

    $stmt = $pdo->prepare("INSERT INTO gestion_practicas_entrevistas 
        (estudiante_id, hito_id, supervisor_id, fecha, comentario, evidencia_url, tipo_supervisor)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([$estudiante_id, $hito_id, $supervisor_id, $fecha, $comentario, $evidencia_url, $tipo_supervisor]);

    header("Location: listar.php?mensaje=Entrevista+registrada+correctamente");
    exit;
}
?>
