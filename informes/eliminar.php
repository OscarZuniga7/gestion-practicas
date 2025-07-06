<?php
require_once('../includes/db.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Preparar y ejecutar la eliminación
        $stmt = $pdo->prepare("DELETE FROM informes WHERE id = ?");
        $stmt->execute([$id]);

        // Redirigir al listado después de eliminar
        header("Location: listar.php");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar el informe: " . $e->getMessage();
    }
} else {
    echo "ID no válido.";
}
