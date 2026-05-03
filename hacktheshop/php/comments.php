<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

$action  = $_GET['action'] ?? '';
$product = $_GET['product'] ?? '';

if ($action === 'get') {
  $stmt = $pdo->prepare("SELECT * FROM comments WHERE product = ? ORDER BY created_at DESC LIMIT 20");
  $stmt->execute([$product]);
  $comments = $stmt->fetchAll();
  echo json_encode($comments);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $author  = htmlspecialchars(trim($_POST['author'] ?? ''), ENT_QUOTES, 'UTF-8');
  $text    = htmlspecialchars(trim($_POST['text'] ?? ''), ENT_QUOTES, 'UTF-8');
  $product = htmlspecialchars(trim($_POST['product'] ?? ''), ENT_QUOTES, 'UTF-8');

  if ($author && $text && $product) {
    $stmt = $pdo->prepare("INSERT INTO comments (author, text, product) VALUES (?, ?, ?)");
    $stmt->execute([$author, $text, $product]);
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'error' => 'All fields required']);
  }
  exit;
}

echo json_encode(['error' => 'Invalid request']);
?>