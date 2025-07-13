<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: composiciones_lista.php');
    exit;
}

$id = $_GET['id'];

// Obtener la composición (para eliminar la imagen también si existe)
$stmt = $pdo->prepare("SELECT imagen FROM composiciones WHERE id = ?");
$stmt->execute([$id]);
$compo = $stmt->fetch();

if ($compo) {
    // Eliminar imagen del servidor si existe
    if (!empty($compo['imagen']) && file_exists($compo['imagen'])) {
        unlink($compo['imagen']);
    }

    // Eliminar de la base de datos
    $stmt = $pdo->prepare("DELETE FROM composiciones WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: composiciones_lista.php');
exit;
