<?php
session_start([
  'cookie_lifetime' => 1800,
  'cookie_secure'   => false,
  'cookie_httponly' => true,
  'cookie_samesite' => 'Strict'
]);
require 'db_connect.php';

// Session timeout after 30 min
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
  session_unset();
  session_destroy();
  header('Location: login_secure.php?timeout=1');
  exit;
}
if (isset($_SESSION['user'])) {
  $_SESSION['last_activity'] = time();
}

$error = '';
$success = '';

if (!isset($_SESSION['login_attempts'])) {
  $_SESSION['login_attempts'] = 0;
  $_SESSION['last_attempt_time'] = time();
}

// Reset after 15 min
if (time() - $_SESSION['last_attempt_time'] > 900) {
  $_SESSION['login_attempts'] = 0;
  $_SESSION['last_attempt_time'] = time();
}

$max_attempts = 5;
$blocked = $_SESSION['login_attempts'] >= $max_attempts;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if ($blocked) {
    $remaining = 900 - (time() - $_SESSION['last_attempt_time']);
    $minutes = ceil($remaining / 60);
    $error = "Too many failed attempts. Please wait $minutes minute(s).";
  } else {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      session_regenerate_id(true);
      $_SESSION['user']          = $user['nom'];
      $_SESSION['email']         = $user['email'];
      $_SESSION['user_id']       = $user['id'];
      $_SESSION['role']          = $user['role'];
      $_SESSION['last_activity'] = time();
      $_SESSION['login_attempts'] = 0;
      header('Location: ../index.html');
      exit;
    } else {
      $_SESSION['login_attempts']++;
      $_SESSION['last_attempt_time'] = time();
      $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
      if ($remaining_attempts > 0) {
        $error = "Invalid email or password. $remaining_attempts attempt(s) remaining.";
      } else {
        $error = "Too many failed attempts. Please wait 15 minutes.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop - Login</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <canvas id="particles"></canvas>
  <div class="wrapper">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <button class="close-sidebar" onclick="toggleSidebar()">✕</button>
      </div>
      <nav class="sidebar-nav">
        <a href="../login.html"><i class="fa fa-user"></i> Login</a>
        <a href="../promotions.html"><i class="fa fa-tag"></i> Promotions</a>
        <a href="../account.php"><i class="fa fa-circle-user"></i> Your Account</a>
        <a href="javascript:void(0)" onclick="toggleCSMenu(event)">
          <i class="fa fa-headset"></i> Customer Service
          <i class="fa fa-chevron-down" id="csChevron"></i>
        </a>
        <div class="sidebar-submenu" id="csSubmenu">
          <a href="../chatbot.php"><i class="fa fa-comment"></i> Chat with Echo</a>
          <a href="../customer-service.html"><i class="fa fa-envelope"></i> Contact Us</a>
        </div>
      </nav>
    </aside>
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>
    <nav class="navbar">
      <div class="nav-left">
        <button class="burger" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
        <a href="login_secure.php" class="nav-login">Login</a>
      </div>
      <div class="nav-center">
        <a href="../index.html" style="text-decoration:none;"><span class="logo">HackTheShop</span></a>
      </div>
      <div class="nav-right">
        <button class="nav-icon"><i class="fa fa-search"></i></button>
        <button class="nav-icon"><i class="fa fa-heart"></i></button>
        <button class="nav-icon"><i class="fa fa-shopping-cart"></i></button>
      </div>
    </nav>

    <section class="login-section">
      <div class="login-box">
        <h2>Welcome Back</h2>
        <p class="login-subtitle">Sign in to your HackTheShop account</p>

        <?php if (isset($_GET['timeout'])): ?>
          <div class="error-msg">⏱️ Session expired. Please sign in again.</div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="error-msg">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login_secure.php" method="POST">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="login-btn" <?php echo $blocked ? 'disabled' : ''; ?>>Sign In</button>
        </form>
        <p class="login-hint">Don't have an account? <a href="register.php">Sign Up</a></p>
      </div>
    </section>

    <footer class="footer">© 2025 HackTheShop — Your one-stop shop for the latest tech & electronics</footer>
  </div>
  <script src="../js/main.js"></script>
</body>
</html>