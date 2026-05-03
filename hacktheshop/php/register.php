<?php
session_start();
require 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nom      = trim($_POST['nom'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm'] ?? '';

  if (!$nom || !$email || !$password) {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } elseif ($password !== $confirm) {
    $error = "Passwords do not match!";
  } else {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $error = "Email already exists!";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (email, password, nom) VALUES (?, ?, ?)");
      $stmt->execute([$email, $hashed, htmlspecialchars($nom, ENT_QUOTES, 'UTF-8')]);
      $success = "Account created! You can now login.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop - Sign Up</title>
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
        <a href="../login.html" class="nav-login">Login</a>
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
        <h2>Create Account</h2>
        <p class="login-subtitle">Join HackTheShop today</p>

        <?php if ($error): ?>
          <div class="error-msg">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="success-msg">✅ <?php echo htmlspecialchars($success); ?></div>
          <a href="login_secure.php" class="login-btn" style="display:block; text-align:center; text-decoration:none; margin-top:16px;">Go to Login</a>
        <?php else: ?>
          <form action="register.php" method="POST">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="nom" placeholder="John Doe" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" name="confirm" placeholder="••••••••" required>
            </div>
            <button type="submit" class="login-btn">Create Account</button>
          </form>
          <p class="login-hint">Already have an account? <a href="login_secure.php">Sign In</a></p>
        <?php endif; ?>
      </div>
    </section>

    <footer class="footer">© 2025 HackTheShop — Your one-stop shop for the latest tech & electronics</footer>
  </div>
  <script src="../js/main.js"></script>
</body>
</html>