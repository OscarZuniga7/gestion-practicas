<?php
include('../includes/db.php');

if (!isset($_GET['id'])) {
    die('ID no proporcionado.');
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM estudiantes WHERE id = ?");
$stmt->execute([$id]);

header('Location: listar.php');
exit();
