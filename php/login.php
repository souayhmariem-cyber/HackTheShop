<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // VOLONTAIREMENT VULNÉRABLE — SQL Injection
  $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
  
  try {
    $result = $pdo->query($query);
    $user = $result->fetch();

    if ($user) {
      $_SESSION['user'] = $user['nom'];
    } else {
      $error = "Invalid email or password!";
    }
  } catch(PDOException $e) {
    $error = "Error : " . $e->getMessage();
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

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <button class="close-sidebar" onclick="toggleSidebar()">✕</button>
      </div>
      <nav class="sidebar-nav">
        <a href="../login.html"><i class="fa fa-user"></i> Login</a>
        <a href="#"><i class="fa fa-tag"></i> Promotions</a>
        <a href="#"><i class="fa fa-circle-user"></i> Your Account</a>
        <a href="#"><i class="fa fa-headset"></i> Customer Service</a>
      </nav>
    </aside>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-left">
        <button class="burger" onclick="toggleSidebar()">
          <i class="fa fa-bars"></i>
        </button>
        <a href="../login.html" class="nav-login">Login</a>
      </div>
      <div class="nav-center">
        <a href="../index.html" style="text-decoration:none;">
          <span class="logo">HackTheShop</span>
        </a>
      </div>
      <div class="nav-right">
        <button class="nav-icon"><i class="fa fa-search"></i></button>
        <button class="nav-icon"><i class="fa fa-heart"></i></button>
        <button class="nav-icon"><i class="fa fa-shopping-cart"></i></button>
      </div>
    </nav>

    <!-- RESULT -->
    <section class="login-section">
      <div class="login-box">

        <?php if (isset($error)): ?>
          <div class="error-msg">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
          <div class="success-msg">
            ✅ Welcome back, <strong><?php echo $_SESSION['user']; ?></strong>!
          </div>
          <a href="../index.html" class="login-btn" style="display:block; text-align:center; text-decoration:none; margin-top:16px;">
            Back to Shop
          </a>
        <?php else: ?>
          <a href="../login.html" class="login-btn" style="display:block; text-align:center; text-decoration:none; margin-top:16px;">
            Try Again
          </a>
        <?php endif; ?>

      </div>
    </section>

    <footer class="footer">
      © 2025 HackTheShop — Educational Cybersecurity Project
    </footer>

  </div>

  <script src="../js/main.js"></script>
</body>
</html>