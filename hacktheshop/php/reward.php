<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── Generate CTF flag for a user+category ────────────────────────────────────
if ($action === 'generate_flag' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']); exit;
  }
  $uid         = (int)$_SESSION['user_id'];
  $category_id = (int)($_POST['category_id'] ?? 0);
  if (!$category_id) { echo json_encode(['success' => false, 'error' => 'Invalid category']); exit; }

  // Already completed?
  $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND category_id = ?");
  $stmt->execute([$uid, $category_id]);
  if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Already completed', 'already_done' => true]); exit;
  }

  // Return existing unredeemed flag if exists
  $stmt = $pdo->prepare("SELECT flag FROM challenge_flags WHERE user_id = ? AND category_id = ? AND redeemed = 0 ORDER BY issued_at DESC LIMIT 1");
  $stmt->execute([$uid, $category_id]);
  $existing = $stmt->fetch();
  if ($existing) {
    echo json_encode(['success' => true, 'flag' => $existing['flag']]); exit;
  }

  // Generate unique flag: HTF{catId-userId-randomhex}
  $random = bin2hex(random_bytes(12));
  $flag   = 'HTF{' . $category_id . '-' . $uid . '-' . $random . '}';
  $stmt   = $pdo->prepare("INSERT INTO challenge_flags (user_id, category_id, flag) VALUES (?, ?, ?)");
  $stmt->execute([$uid, $category_id, $flag]);

  echo json_encode(['success' => true, 'flag' => $flag]);
  exit;
}

// ── Validate flag + claim reward ─────────────────────────────────────────────
if ($action === 'claim' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']); exit;
  }
  $uid         = (int)$_SESSION['user_id'];
  $category_id = (int)($_POST['category_id'] ?? 0);
  $flag_input  = trim($_POST['flag'] ?? '');

  if (!$category_id) { echo json_encode(['success' => false, 'error' => 'Invalid category']); exit; }

  // Already completed?
  $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND category_id = ?");
  $stmt->execute([$uid, $category_id]);
  if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Already completed', 'already_done' => true]); exit;
  }

  // Flag required
  if (!$flag_input) {
    echo json_encode(['success' => false, 'error' => 'Please enter your flag.', 'need_flag' => true]); exit;
  }

  // Validate flag belongs to this user+category and is unused
  $stmt = $pdo->prepare("SELECT * FROM challenge_flags WHERE user_id = ? AND category_id = ? AND flag = ? AND redeemed = 0");
  $stmt->execute([$uid, $category_id, $flag_input]);
  $flagRow = $stmt->fetch();
  if (!$flagRow) {
    echo json_encode(['success' => false, 'error' => 'Invalid flag! Copy it exactly from the challenge page.', 'flag_invalid' => true]); exit;
  }

  // Mark flag redeemed
  $stmt = $pdo->prepare("UPDATE challenge_flags SET redeemed = 1, redeemed_at = NOW() WHERE id = ?");
  $stmt->execute([$flagRow['id']]);

  // Random product from category
  $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY RAND() LIMIT 1");
  $stmt->execute([$category_id]);
  $product = $stmt->fetch();
  if (!$product) { echo json_encode(['success' => false, 'error' => 'No products in this category']); exit; }

  // Save progress + reward
  $stmt = $pdo->prepare("INSERT IGNORE INTO user_progress (user_id, category_id) VALUES (?, ?)");
  $stmt->execute([$uid, $category_id]);
  $stmt = $pdo->prepare("INSERT INTO rewards (user_id, product_id, category_id) VALUES (?, ?, ?)");
  $stmt->execute([$uid, $product['id'], $category_id]);

  // Award badge
  $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
  $stmt->execute([$category_id]);
  $cat        = $stmt->fetch();
  $badge_name = ($cat['name'] ?? 'Unknown') . ' Master';
  $stmt = $pdo->prepare("SELECT id FROM badges WHERE user_id = ? AND badge_name = ?");
  $stmt->execute([$uid, $badge_name]);
  if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO badges (user_id, badge_name, badge_icon) VALUES (?, ?, ?)");
    $stmt->execute([$uid, $badge_name, 'fa-trophy']);
  }

  echo json_encode(['success' => true, 'product' => $product, 'badge' => $badge_name]);
  exit;
}

// ── Full progress for account page ───────────────────────────────────────────
if ($action === 'get_progress') {
  if (!isset($_SESSION['user_id'])) {
    echo json_encode(['completed' => [], 'rewards' => [], 'badges' => [], 'total_categories' => 8]); exit;
  }
  $uid = (int)$_SESSION['user_id'];

  $stmt = $pdo->prepare("SELECT category_id FROM user_progress WHERE user_id = ?");
  $stmt->execute([$uid]);
  $completed = array_column($stmt->fetchAll(), 'category_id');

  $stmt = $pdo->prepare("
    SELECT r.*, p.name, p.img, p.price, c.name AS category_name, c.slug
    FROM rewards r
    JOIN products p ON p.id = r.product_id
    JOIN categories c ON c.id = r.category_id
    WHERE r.user_id = ?
    ORDER BY r.won_at DESC
  ");
  $stmt->execute([$uid]);
  $rewards = $stmt->fetchAll();

  $stmt = $pdo->prepare("SELECT * FROM badges WHERE user_id = ? ORDER BY earned_at DESC");
  $stmt->execute([$uid]);
  $badges = $stmt->fetchAll();

  $total = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

  echo json_encode(['completed' => $completed, 'rewards' => $rewards, 'badges' => $badges, 'total_categories' => $total]);
  exit;
}

// ── Categories with unlock status ────────────────────────────────────────────
if ($action === 'get_categories') {
  $uid        = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
  $stmt       = $pdo->query("SELECT * FROM categories ORDER BY id");
  $categories = $stmt->fetchAll();
  $completed  = [];
  if ($uid) {
    $stmt = $pdo->prepare("SELECT category_id FROM user_progress WHERE user_id = ?");
    $stmt->execute([$uid]);
    $completed = array_column($stmt->fetchAll(), 'category_id');
  }
  foreach ($categories as &$cat) {
    $cat['unlocked'] = in_array($cat['id'], $completed);
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM products WHERE category_id = ?");
    $stmt->execute([$cat['id']]);
    $cat['product_count'] = (int)$stmt->fetch()['cnt'];
  }
  echo json_encode($categories);
  exit;
}

// ── Badges ────────────────────────────────────────────────────────────────────
if ($action === 'get_badges') {
  if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
  $uid  = (int)$_SESSION['user_id'];
  $stmt = $pdo->prepare("SELECT * FROM badges WHERE user_id = ? ORDER BY earned_at DESC");
  $stmt->execute([$uid]);
  echo json_encode($stmt->fetchAll());
  exit;
}

echo json_encode(['error' => 'Unknown action']);
?>