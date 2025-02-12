<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Get client ID from URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: clients.php");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: clients.php");
    exit();
} catch(PDOException $e) {
    echo "Error deleting client: " . $e->getMessage();
    exit();
}
?>
