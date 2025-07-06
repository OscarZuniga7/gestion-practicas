<?php
include('../includes/db.php');

// Validar que venga un ID
if (!isset($_GET['id'])) {
    die("ID de hito no especificado.");
}

// Preparar y ejecutar eliminación
$stmt = $pdo->prepare("DELETE FROM hitos WHERE id = ?");
$resultado = $stmt->execute([$_GET['id']]);

if ($resultado) {
    header("Location: listar.php");
    exit;
} else {
    echo "Error al eliminar el hito.";
}
?>
