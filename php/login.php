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
      header('Location: ../index.html');
      exit;
    } else {
      $error = "Email ou mot de passe incorrect !";
    }
  } catch(PDOException $e) {
    $error = "Erreur : " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultat Connexion</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <nav class="navbar">
    <span class="logo">HackTheShop</span>
    <ul class="nav-links">
      <li><a href="../index.html">Accueil</a></li>
      <li><a href="../products.html">Produits</a></li>
      <li><a href="../login.html">Connexion</a></li>
      <li><a href="../search.html">Recherche</a></li>
    </ul>
    <button class="cart-btn">🛒 Panier (0)</button>
  </nav>

  <section class="login-section">
    <div class="login-box">
      <?php if (isset($error)): ?>
        <div class="error-msg">❌ <?php echo $error; ?></div>
      <?php endif; ?>

      <?php if (isset($_SESSION['user'])): ?>
        <div class="success-msg">✅ Bienvenue <?php echo $_SESSION['user']; ?> !</div>
      <?php endif; ?>

      <a href="../login.html" class="login-btn" style="display:block; text-align:center; text-decoration:none; margin-top:16px;">
        Retour à la connexion
      </a>
    </div>
  </section>

  <footer class="footer">
    © 2025 HackTheShop — Projet éducatif cybersécurité
  </footer>

</body>
</html>