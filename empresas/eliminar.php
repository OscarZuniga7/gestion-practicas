<?php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    die('ID no proporcionado.');
}

$id = $_GET['id'];

// Eliminar empresa
$stmt = $pdo->prepare("DELETE FROM empresas WHERE id = ?");
$stmt->execute([$id]);

header('Location: listar.php');
exit();
