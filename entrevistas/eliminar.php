<?php
// entrevistas/eliminar.php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    die('ID de entrevista no especificado.');
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM entrevistas WHERE id = ?");
$stmt->execute([$id]);

header('Location: listar.php');
exit();
