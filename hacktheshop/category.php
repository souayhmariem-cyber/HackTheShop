<?php
session_start();
require 'php/db_connect.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: index.html'); exit; }

// Get category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute([$slug]);
$category = $stmt->fetch();
if (!$category) { header('Location: index.html'); exit; }

// Get products for this category
$stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY name");
$stmt->execute([$category['id']]);
$products = $stmt->fetchAll();

// Check if user completed this category
$isCompleted = false;
$userReward  = null;
$userId      = $_SESSION['user_id'] ?? 0;

if ($userId) {
  $stmt = $pdo->prepare("SELECT * FROM user_progress WHERE user_id = ? AND category_id = ?");
  $stmt->execute([$userId, $category['id']]);
  $isCompleted = (bool)$stmt->fetch();

  if ($isCompleted) {
    $stmt = $pdo->prepare("
      SELECT r.*, p.name AS product_name, p.img, p.price, p.description
      FROM rewards r
      JOIN products p ON p.id = r.product_id
      WHERE r.user_id = ? AND r.category_id = ?
      LIMIT 1
    ");
    $stmt->execute([$userId, $category['id']]);
    $userReward = $stmt->fetch();
  }
}

$categoryIcons = [
  'pc'        => 'fa-laptop',
  'phone'     => 'fa-mobile',
  'audio'     => 'fa-headphones',
  'wearable'  => 'fa-clock',
  'camera'    => 'fa-camera',
  'gaming'    => 'fa-gamepad',
  'accessory' => 'fa-plug',
  'screen'    => 'fa-tv',
];
$icon = $categoryIcons[$slug] ?? 'fa-star';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop — <?php echo htmlspecialchars($category['name']); ?></title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="images/favicon.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ── Category hero ─────────────────────────── */
    .cat-hero {
      padding: 48px 24px 32px;
      text-align: center;
      position: relative;
    }
    .cat-hero-icon {
      font-size: 3rem;
      color: #7c3aed;
      margin-bottom: 12px;
      filter: drop-shadow(0 0 20px rgba(124,58,237,0.5));
    }
    .cat-hero h1 {
      color: #e2d9f3;
      font-size: 2rem;
      letter-spacing: 3px;
      margin-bottom: 8px;
    }
    .cat-hero p {
      color: #534AB7;
      font-size: 0.9rem;
    }

    /* ── Challenge Gate ─────────────────────────── */
    .challenge-gate {
      max-width: 720px;
      margin: 0 auto 40px;
      padding: 0 20px;
    }

    .challenge-box {
      background: linear-gradient(135deg, rgba(124,58,237,0.12), rgba(34,211,238,0.06));
      border: 2px solid #7c3aed;
      border-radius: 20px;
      padding: 32px;
      position: relative;
      overflow: hidden;
    }
    .challenge-box::before {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 150px; height: 150px;
      background: rgba(124,58,237,0.08);
      border-radius: 50%;
    }

    .challenge-box.completed {
      border-color: #22d3ee;
      background: linear-gradient(135deg, rgba(34,211,238,0.08), rgba(124,58,237,0.05));
    }

    .challenge-step {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 20px;
    }
    .step-num {
      width: 32px; height: 32px;
      border-radius: 50%;
      border: 2px solid #7c3aed;
      color: #a78bfa;
      display: flex; align-items: center; justify-content: center;
      font-size: 0.85rem;
      font-weight: bold;
      flex-shrink: 0;
    }
    .step-num.done { border-color: #22d3ee; color: #22d3ee; }
    .step-content h4 {
      color: #e2d9f3;
      font-size: 0.95rem;
      margin-bottom: 4px;
    }
    .step-content p {
      color: #534AB7;
      font-size: 0.82rem;
      line-height: 1.5;
    }

    .challenge-title-row {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }
    .challenge-title-row .icon {
      font-size: 1.8rem;
      color: #7c3aed;
    }
    .challenge-title-row h3 {
      color: #a78bfa;
      font-size: 1.1rem;
      letter-spacing: 1px;
    }
    .challenge-title-row .badge {
      margin-left: auto;
      font-size: 0.72rem;
      padding: 4px 12px;
      border-radius: 20px;
      border: 1px solid #7c3aed;
      color: #a78bfa;
    }
    .challenge-title-row .badge.done {
      border-color: #22d3ee;
      color: #22d3ee;
    }

    .btn-start-challenge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: #7c3aed;
      border: none;
      color: white;
      border-radius: 25px;
      cursor: pointer;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      font-size: 0.95rem;
      letter-spacing: 1px;
      transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
      text-decoration: none;
    }
    .btn-start-challenge:hover {
      background: #6d28d9;
      box-shadow: 0 0 30px rgba(124,58,237,0.5);
      transform: translateY(-2px);
    }

    .btn-claim {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: linear-gradient(135deg, #7c3aed, #22d3ee);
      border: none;
      color: white;
      border-radius: 25px;
      cursor: pointer;
      font-family: 'Courier New', monospace;
      font-weight: bold;
      font-size: 0.95rem;
      letter-spacing: 1px;
      transition: opacity 0.2s, transform 0.15s;
      animation: pulseClaim 2s ease-in-out infinite;
    }
    .btn-claim:hover { opacity: 0.9; transform: translateY(-2px); }
    @keyframes pulseClaim {
      0%,100% { box-shadow: 0 0 0 0 rgba(124,58,237,0.4); }
      50%      { box-shadow: 0 0 0 12px rgba(124,58,237,0); }
    }

    /* ── Products section ───────────────────────── */
    .products-section {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px 60px;
    }
    .products-section-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }
    .products-section-header h2 {
      color: #e2d9f3;
      font-size: 1.2rem;
      letter-spacing: 2px;
    }
    .unlock-status {
      margin-left: auto;
      font-size: 0.8rem;
      padding: 5px 14px;
      border-radius: 20px;
    }
    .unlock-status.locked   { border: 1px solid #534AB7; color: #534AB7; }
    .unlock-status.unlocked { border: 1px solid #22d3ee; color: #22d3ee; background: rgba(34,211,238,0.08); }

    /* ── Locked product overlay ──────────────────── */
    .product-card.locked-card {
      position: relative;
    }
    .product-card.locked-card::after {
      content: '🔒 Solve Challenge to Unlock';
      position: absolute;
      inset: 0;
      background: rgba(8, 8, 16, 0.75);
      backdrop-filter: blur(3px);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #a78bfa;
      font-size: 0.78rem;
      font-family: 'Courier New', monospace;
      border-radius: inherit;
      letter-spacing: 0.5px;
      text-align: center;
      cursor: not-allowed;
    }

    /* ── Won reward highlight ───────────────────── */
    .reward-won-card {
      background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(34,211,238,0.08));
      border: 2px solid #22d3ee !important;
      position: relative;
    }
    .reward-won-badge {
      position: absolute;
      top: 10px; right: 10px;
      background: #22d3ee;
      color: #0d0d14;
      font-size: 0.7rem;
      font-weight: bold;
      padding: 4px 10px;
      border-radius: 20px;
      font-family: 'Courier New', monospace;
      z-index: 2;
    }

    /* ── Spinner overlay ────────────────────────── */
    #spinnerOverlay {
      position: fixed; inset: 0;
      background: rgba(8,8,16,0.96);
      display: none;
      flex-direction: column;
      align-items: center; justify-content: center;
      z-index: 10000;
      font-family: 'Courier New', monospace;
    }
    @keyframes spin360 { to { transform: rotate(360deg); } }
    .spin-ring {
      width: 120px; height: 120px; border-radius: 50%;
      border: 4px solid #1e1a2e;
      border-top-color: #7c3aed;
      border-right-color: #a78bfa;
      animation: spin360 0.8s linear infinite;
    }
    .spin-ring-inner {
      width: 80px; height: 80px; border-radius: 50%;
      border: 3px solid #1e1a2e;
      border-top-color: #22d3ee;
      animation: spin360 0.6s linear infinite reverse;
      position: absolute;
      top: 50%; left: 50%; transform: translate(-50%,-50%);
    }
    .spin-wrap { position: relative; width: 120px; height: 120px; margin-bottom: 28px; }
    @keyframes pulseText { 0%,100%{opacity:1} 50%{opacity:0.5} }
    .spin-label { color: #a78bfa; font-size: 1rem; font-weight: bold; letter-spacing: 2px; animation: pulseText 1s ease-in-out infinite; }

    /* ── Reward modal ───────────────────────────── */
    #rewardModal {
      position: fixed; inset: 0;
      background: rgba(8,8,16,0.92);
      display: none;
      align-items: center; justify-content: center;
      z-index: 10001; padding: 20px;
    }
    @keyframes popIn { from{transform:scale(0.4);opacity:0} to{transform:scale(1);opacity:1} }
    .reward-modal-box {
      background: linear-gradient(135deg, #0d0d14, #13131f);
      border: 2px solid #7c3aed;
      border-radius: 22px;
      max-width: 400px; width: 100%;
      text-align: center; padding: 36px 28px;
      animation: popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275);
      position: relative; overflow: hidden;
      box-shadow: 0 0 80px rgba(124,58,237,0.4);
      font-family: 'Courier New', monospace;
    }
    @keyframes confettiFall {
      0%   { transform: translateY(-10px) rotate(0);   opacity: 1; }
      100% { transform: translateY(220px) rotate(720deg); opacity: 0; }
    }
    .conf-dot {
      position: absolute; width: 8px; height: 8px; border-radius: 50%;
      animation: confettiFall 1.6s ease-in forwards;
      pointer-events: none;
    }

    /* ── Breadcrumb ─────────────────────────────── */
    .breadcrumb {
      padding: 12px 24px;
      color: #534AB7;
      font-size: 0.82rem;
      font-family: 'Courier New', monospace;
    }
    .breadcrumb a { color: #7c3aed; text-decoration: none; }
    .breadcrumb a:hover { color: #a78bfa; }
    .breadcrumb span { margin: 0 8px; }
  </style>
</head>
<body>
  <canvas id="particles"></canvas>

  <!-- SPINNER OVERLAY -->
  <div id="spinnerOverlay">
    <div class="spin-wrap">
      <div class="spin-ring"></div>
      <div class="spin-ring-inner"></div>
    </div>
    <div class="spin-label">🎰 DRAWING YOUR REWARD...</div>
    <div style="color:#534AB7;font-size:0.8rem;margin-top:10px;">Category: <?php echo htmlspecialchars($category['name']); ?></div>
  </div>

  <!-- REWARD MODAL -->
  <div id="rewardModal">
    <div class="reward-modal-box" id="rewardModalBox">
      <div id="confContainer" style="position:absolute;top:0;left:0;right:0;pointer-events:none;"></div>
      <div style="font-size:3rem;margin-bottom:8px;">🎉</div>
      <h2 style="color:#a78bfa;margin-bottom:4px;font-size:1.4rem;">YOU WON!</h2>
      <p style="color:#534AB7;font-size:0.82rem;margin-bottom:20px;">Challenge completed — reward unlocked</p>
      <img id="rewardImg" src="" alt="" style="width:100%;max-width:220px;height:150px;object-fit:cover;border-radius:14px;border:2px solid #7c3aed;margin-bottom:16px;">
      <div id="rewardName" style="color:#e2d9f3;font-size:1.1rem;font-weight:bold;margin-bottom:6px;"></div>
      <div id="rewardPrice" style="color:#a78bfa;font-size:1.4rem;font-weight:bold;margin-bottom:6px;"></div>
      <div id="rewardBadge" style="display:inline-block;background:rgba(124,58,237,0.2);border:1px solid #7c3aed;color:#a78bfa;padding:5px 14px;border-radius:20px;font-size:0.78rem;margin-bottom:22px;"></div>
      <br>
      <button onclick="closeRewardModal()" style="background:#7c3aed;border:none;color:white;padding:13px 36px;border-radius:25px;cursor:pointer;font-family:inherit;font-weight:bold;font-size:0.9rem;transition:background 0.2s;" onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
        Awesome! 🚀
      </button>
    </div>
  </div>

  <div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <button class="close-sidebar" onclick="toggleSidebar()">✕</button>
      </div>
      <nav class="sidebar-nav">
        <a href="login.html"><i class="fa fa-user"></i> Login</a>
        <a href="promotions.html"><i class="fa fa-tag"></i> Promotions</a>
        <a href="index.html"><i class="fa fa-home"></i> Shop</a>
        <a href="account.php"><i class="fa fa-circle-user"></i> Your Account</a>
        <a href="javascript:void(0)" onclick="toggleCSMenu(event)">
          <i class="fa fa-headset"></i> Customer Service
          <i class="fa fa-chevron-down" id="csChevron"></i>
        </a>
        <div class="sidebar-submenu" id="csSubmenu">
          <a href="chatbot.php"><i class="fa fa-comment"></i> Chat with Echo</a>
        </div>
      </nav>
    </aside>
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-left">
        <button class="burger" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
        <?php if ($userId): ?>
          <span class="nav-login" style="color:#22d3ee;font-size:0.82rem;">
            👤 <?php echo htmlspecialchars($_SESSION['user']); ?>
          </span>
        <?php else: ?>
          <a href="login.html" class="nav-login">Login</a>
        <?php endif; ?>
      </div>
      <div class="nav-center">
        <a href="index.html" style="text-decoration:none;"><span class="logo">HackTheShop</span></a>
      </div>
      <div class="nav-right">
        <button class="nav-icon" onclick="toggleSearch()"><i class="fa fa-search"></i></button>
        <button class="nav-icon" onclick="toggleWishlist()"><i class="fa fa-heart"></i></button>
        <button class="nav-icon" onclick="toggleCart()">
          <i class="fa fa-shopping-cart"></i>
          <span class="cart-count" id="cartCount">0</span>
        </button>
      </div>
    </nav>

    <div class="search-dropdown" id="searchDropdown">
      <input type="text" id="searchInput" placeholder="Search products...">
      <button onclick="searchProducts()"><i class="fa fa-search"></i></button>
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
      <a href="index.html"><i class="fa fa-home"></i> Home</a>
      <span>›</span>
      <a href="index.html">Shop</a>
      <span>›</span>
      <strong style="color:#a78bfa;"><?php echo htmlspecialchars($category['name']); ?></strong>
    </div>

    <!-- CATEGORY HERO -->
    <div class="cat-hero">
      <div class="cat-hero-icon"><i class="fa <?php echo $icon; ?>"></i></div>
      <h1><?php echo htmlspecialchars($category['name']); ?></h1>
      <p><?php echo count($products); ?> products available — solve the challenge to unlock them all</p>
    </div>

    <!-- ════════════════════════════════════════════
         CHALLENGE GATE — THE CORE INTEGRATION
         ════════════════════════════════════════════ -->
    <div class="challenge-gate">
      <div class="challenge-box <?php echo $isCompleted ? 'completed' : ''; ?>">

        <div class="challenge-title-row">
          <div class="icon"><i class="fa fa-shield-halved"></i></div>
          <h3><?php echo htmlspecialchars($category['challenge_title'] ?? 'Cybersecurity Challenge'); ?></h3>
          <div class="badge <?php echo $isCompleted ? 'done' : ''; ?>">
            <?php echo $isCompleted ? '✓ COMPLETED' : '🔒 LOCKED'; ?>
          </div>
        </div>

        <p style="color:#e2d9f3;font-size:0.88rem;line-height:1.6;margin-bottom:24px;">
          <?php echo htmlspecialchars($category['challenge_description'] ?? 'Complete this challenge to unlock all products in this category.'); ?>
        </p>

        <?php if (!$isCompleted): ?>
          <!-- STEP 1: Go solve it -->
          <div class="challenge-step">
            <div class="step-num">1</div>
            <div class="step-content">
              <h4>Go to the challenge</h4>
              <p>Opens in a new tab on HackerRank. Solve it, then come back here.</p>
            </div>
          </div>
          <!-- STEP 2: Claim -->
          <div class="challenge-step">
            <div class="step-num">2</div>
            <div class="step-content">
              <h4>Claim your reward</h4>
              <p>After solving, click "I Completed It!" below to spin the wheel and win a random product from this category.</p>
            </div>
          </div>

          <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:8px;">
            <a href="<?php echo htmlspecialchars($category['challenge_url']); ?>"
               target="_blank"
               class="btn-start-challenge"
               onclick="onChallengeStart()">
              <i class="fa fa-external-link-alt"></i> Start Challenge
            </a>

            <button class="btn-claim"
                    id="claimBtn"
                    onclick="claimReward()"
                    style="display:none;">
              <i class="fa fa-gift"></i> I Completed It!
            </button>
          </div>

          <?php if (!$userId): ?>
            <div style="margin-top:16px;padding:12px 16px;background:rgba(124,58,237,0.1);border:1px solid #7c3aed;border-radius:10px;font-size:0.82rem;color:#a78bfa;">
              ⚠️ You need to <a href="login.html" style="color:#22d3ee;text-decoration:none;font-weight:bold;">log in</a> before claiming your reward.
            </div>
          <?php endif; ?>

        <?php else: ?>
          <!-- ALREADY COMPLETED -->
          <div style="display:flex;align-items:center;gap:12px;padding:16px;background:rgba(34,211,238,0.08);border:1px solid #22d3ee;border-radius:12px;margin-bottom:20px;">
            <i class="fa fa-trophy" style="color:#22d3ee;font-size:1.5rem;"></i>
            <div>
              <div style="color:#22d3ee;font-weight:bold;margin-bottom:2px;">Challenge Completed! 🏆</div>
              <div style="color:#534AB7;font-size:0.82rem;">All <?php echo count($products); ?> products in this category are now unlocked.</div>
            </div>
          </div>

          <?php if ($userReward): ?>
            <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(13,13,20,0.8);border:1px solid #2d2d40;border-radius:12px;">
              <img src="<?php echo htmlspecialchars($userReward['img']); ?>" alt="<?php echo htmlspecialchars($userReward['product_name']); ?>"
                   style="width:70px;height:55px;object-fit:cover;border-radius:8px;border:1px solid #7c3aed;">
              <div>
                <div style="color:#534AB7;font-size:0.75rem;margin-bottom:2px;">🎁 YOUR WON REWARD</div>
                <div style="color:#e2d9f3;font-weight:bold;font-size:0.9rem;"><?php echo htmlspecialchars($userReward['product_name']); ?></div>
                <div style="color:#a78bfa;font-size:0.88rem;">$<?php echo number_format($userReward['price'], 2); ?></div>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
    <!-- END CHALLENGE GATE -->

    <!-- ════════════════════════════════════════════
         PRODUCTS GRID
         ════════════════════════════════════════════ -->
    <div class="products-section">
      <div class="products-section-header">
        <i class="fa <?php echo $icon; ?>" style="color:#7c3aed;font-size:1.2rem;"></i>
        <h2><?php echo htmlspecialchars($category['name']); ?> Products</h2>
        <span class="unlock-status <?php echo $isCompleted ? 'unlocked' : 'locked'; ?>">
          <?php echo $isCompleted ? '🔓 Unlocked' : '🔒 Locked'; ?>
        </span>
      </div>

      <?php if (!$isCompleted): ?>
        <p style="color:#534AB7;font-size:0.85rem;margin-bottom:20px;text-align:center;">
          Complete the challenge above to unlock all products and be able to add them to cart.
        </p>
      <?php endif; ?>

      <div class="products-grid">
        <?php foreach ($products as $product): ?>
          <?php $isWon = $userReward && $userReward['product_name'] === $product['name']; ?>
          <div class="product-card <?php echo !$isCompleted ? 'locked-card' : ''; ?> <?php echo $isWon ? 'reward-won-card' : ''; ?>">
            <?php if ($isWon): ?>
              <div class="reward-won-badge">🎁 WON</div>
            <?php endif; ?>
            <img class="product-img"
                 src="<?php echo htmlspecialchars($product['img']); ?>"
                 alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="product-info">
              <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
              <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
              <?php if ($isCompleted): ?>
                <div class="product-actions">
                  <button class="btn-cart"
                    onclick="addToCart('<?php echo htmlspecialchars(addslashes($product['name'])); ?>','$<?php echo number_format($product['price'],2); ?>')">
                    <i class="fa fa-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn-wish" onclick="toggleWish(this)"><i class="fa fa-heart"></i></button>
                </div>
              <?php else: ?>
                <div style="color:#534AB7;font-size:0.78rem;margin-top:8px;display:flex;align-items:center;gap:6px;">
                  <i class="fa fa-lock"></i> Solve challenge to unlock
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- PANELS -->
    <div class="panel" id="cartPanel">
      <div class="panel-header">🛒 Cart<button onclick="toggleCart()">✕</button></div>
      <div class="panel-body" id="cartItems"><p class="empty-msg">Your cart is empty.</p></div>
    </div>
    <div class="panel" id="wishPanel">
      <div class="panel-header">❤️ Wishlist<button onclick="toggleWishlist()">✕</button></div>
      <div class="panel-body" id="wishItems"><p class="empty-msg">Your wishlist is empty.</p></div>
    </div>

    <!-- CHATBOT -->
    <div class="chatbot-bubble" onclick="toggleChatbot()">
      <img src="images/echo.png" alt="Echo" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
    </div>
    <div class="chatbot-frame" id="chatbotFrame">
      <iframe src="chatbot.php" frameborder="0"></iframe>
    </div>

    <footer class="footer">© 2025 HackTheShop — Your one-stop shop for the latest tech & electronics</footer>
  </div>

  <script src="js/main.js"></script>
  <script>
    const CATEGORY_ID   = <?php echo (int)$category['id']; ?>;
    const IS_LOGGED_IN  = <?php echo $userId ? 'true' : 'false'; ?>;
    const IS_COMPLETED  = <?php echo $isCompleted ? 'true' : 'false'; ?>;

    // ── Show claim button after clicking Start Challenge ──────────────────────
    function onChallengeStart() {
      setTimeout(() => {
        const btn = document.getElementById('claimBtn');
        if (btn) {
          btn.style.display = 'inline-flex';
          btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }, 3000);
      sessionStorage.setItem('challengeStarted_' + CATEGORY_ID, Date.now());
    }

    // ── On page load: restore claim button if they already clicked ────────────
    window.addEventListener('DOMContentLoaded', () => {
      const started = sessionStorage.getItem('challengeStarted_' + CATEGORY_ID);
      if (started && !IS_COMPLETED) {
        const btn = document.getElementById('claimBtn');
        if (btn) btn.style.display = 'inline-flex';
      }
    });

    // ── Claim Reward ──────────────────────────────────────────────────────────
    async function claimReward() {
      if (!IS_LOGGED_IN) {
        window.location.href = 'login.html';
        return;
      }

      // Show spinner
      document.getElementById('spinnerOverlay').style.display = 'flex';

      try {
        const res  = await fetch('php/reward.php', {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    `action=claim&category_id=${CATEGORY_ID}`
        });
        const data = await res.json();

        setTimeout(() => {
          document.getElementById('spinnerOverlay').style.display = 'none';

          if (data.success) {
            sessionStorage.removeItem('challengeStarted_' + CATEGORY_ID);
            showRewardModal(data.product, data.badge);
          } else if (data.already_done) {
            showToast('⚠️ You already completed this challenge!');
            setTimeout(() => location.reload(), 1500);
          } else if (data.error === 'Not logged in') {
            window.location.href = 'login.html';
          } else {
            showToast('❌ ' + (data.error || 'Something went wrong.'));
          }
        }, 2800);

      } catch(e) {
        document.getElementById('spinnerOverlay').style.display = 'none';
        showToast('❌ Connection error. Try again.');
      }
    }

    // ── Reward Modal ──────────────────────────────────────────────────────────
    function showRewardModal(product, badgeName) {
      document.getElementById('rewardImg').src          = product.img;
      document.getElementById('rewardName').textContent = product.name;
      document.getElementById('rewardPrice').textContent= '$' + parseFloat(product.price).toFixed(2);
      document.getElementById('rewardBadge').textContent= badgeName ? '🏆 ' + badgeName : '';
      document.getElementById('rewardModal').style.display = 'flex';

      // Confetti
      const cc     = document.getElementById('confContainer');
      const colors = ['#7c3aed','#a78bfa','#22d3ee','#f472b6','#fbbf24','#34d399'];
      cc.innerHTML = '';
      for (let i = 0; i < 35; i++) {
        const d = document.createElement('div');
        d.className = 'conf-dot';
        d.style.cssText = `left:${Math.random()*100}%;background:${colors[Math.floor(Math.random()*colors.length)]};animation-delay:${Math.random()*0.9}s;animation-duration:${1.3+Math.random()*0.9}s;`;
        cc.appendChild(d);
      }
    }

    function closeRewardModal() {
      document.getElementById('rewardModal').style.display = 'none';
      location.reload(); // Refresh to show unlocked products
    }

    // ── Toast helper (in case main.js doesn't define it yet) ──────────────────
    if (typeof showToast === 'undefined') {
      window.showToast = function(msg) {
        let t = document.getElementById('hts-toast');
        if (!t) {
          t = document.createElement('div');
          t.id = 'hts-toast';
          t.style.cssText = 'position:fixed;bottom:100px;right:20px;background:rgba(19,19,31,0.95);border:1px solid #7c3aed;color:#e2d9f3;padding:12px 20px;border-radius:12px;font-family:Courier New,monospace;font-size:0.85rem;z-index:9999;opacity:0;transition:opacity 0.3s;max-width:280px;';
          document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        clearTimeout(t._to);
        t._to = setTimeout(() => { t.style.opacity = '0'; }, 2500);
      };
    }
  </script>
</body>
</html>