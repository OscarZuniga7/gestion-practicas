<?php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    echo "ID de evaluación no especificado.";
    exit;
}

$id = $_GET['id'];

// Eliminar evaluación
$stmt = $pdo->prepare("DELETE FROM evaluaciones WHERE id = ?");
$stmt->execute([$id]);

// Redireccionar al listado
header("Location: listar.php");
exit;
