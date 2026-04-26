<?php

session_start();
header('Content-Type: application/json');

require 'db_connection.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';


if ($action === 'get') {
    $productId = $_GET['product_id'] ?? 1;

    if (!is_numeric($productId)) {
        echo json_encode([]);
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT id, auteur, contenu, created_at
             FROM comments
             WHERE product_id = ?
             ORDER BY created_at DESC
             LIMIT 50"
        );
        $stmt->execute([intval($productId)]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        

    } catch (PDOException $e) {
        echo json_encode(['error' => 'DB error: ' . $e->getMessage()]);
    }
    exit;
}

// ── POST: save a new comment ────────────────────────────────
if ($action === 'post') {
    $auteur    = $_POST['auteur']     ?? '';
    $contenu   = $_POST['contenu']    ?? '';
    $productId = $_POST['product_id'] ?? 1;

    if (empty($auteur) || empty($contenu)) {
        echo json_encode(['success' => false, 'error' => 'Missing fields.']);
        exit;
    }

    if (!is_numeric($productId)) {
        $productId = 1;
    }

    try {
        // ─────────────────────────────────────────────────────
        // INTENTIONALLY VULNERABLE — storing raw user input
        // No strip_tags(), no htmlspecialchars(), no sanitization
        //
        // Secure version:
        //   $auteur  = htmlspecialchars(strip_tags($auteur),  ENT_QUOTES, 'UTF-8');
        //   $contenu = htmlspecialchars(strip_tags($contenu), ENT_QUOTES, 'UTF-8');
        // ─────────────────────────────────────────────────────
        $stmt = $pdo->prepare(
            "INSERT INTO comments (product_id, auteur, contenu)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([intval($productId), $auteur, $contenu]);

        // ── Detect stored XSS payload ──
        $xssPatterns = ['<script', '<img', '<svg', 'onerror', 'onload', 'javascript:', 'alert(', 'document.cookie'];

        foreach ($xssPatterns as $pattern) {
            if (stripos($contenu, $pattern) !== false) {
                $_SESSION['attack'] = 'xss_stored';
                break;
            }
        }

        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'DB error: ' . $e->getMessage()]);
    }
    exit;
}

// ── Fallback ────────────────────────────────────────────────
echo json_encode(['error' => 'Unknown action.']);
?>