<?php
include('../includes/db.php');

// Verificar que se haya pasado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID de supervisor no válido.</div>";
    exit;
}

$id = $_GET['id'];

// Verificar si el supervisor existe
$stmt = $pdo->prepare("SELECT * FROM supervisores WHERE id = ?");
$stmt->execute([$id]);
$supervisor = $stmt->fetch();

if (!$supervisor) {
    echo "<div class='alert alert-warning'>El supervisor con ID $id no existe.</div>";
    exit;
}

// Eliminar supervisor
$stmt = $pdo->prepare("DELETE FROM supervisores WHERE id = ?");
$stmt->execute([$id]);

// Redirigir de vuelta al listado
header("Location: listar.php");
exit;
?>
