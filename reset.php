<?php
session_start();
header('Content-Type: application/json');

require '../db-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM lab_progress WHERE session_id = ?");
        $stmt->execute([session_id()]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['error' => 'POST only']);
}
?>