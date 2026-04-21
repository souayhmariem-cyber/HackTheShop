<?php
$search = '';
$results = [];

if (isset($_GET['q'])) {
  $search = $_GET['q'];
}

$products = [
  ['nom' => 'Laptop Pro X', 'prix' => '799€', 'emoji' => '💻'],
  ['nom' => 'Smartphone Z12', 'prix' => '499€', 'emoji' => '📱'],
  ['nom' => 'Casque Audio Pro', 'prix' => '149€', 'emoji' => '🎧'],
  ['nom' => 'Montre Connect', 'prix' => '249€', 'emoji' => '⌚'],
];

foreach ($products as $product) {
  if (stripos($product['nom'], $search) !== false) {
    $results[] = $product;
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>HackTheShop - Recherche</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <nav class="navbar">
    <span class="logo">HackTheShop</span>
    <ul class="nav-links">
      <li><a href="../index.html">Accueil</a></li>
      <li><a href="../products.html">Produits</a></li>
      <li><a href="../login.html">Connexion</a></li>
      <li><a href="search.php">Recherche</a></li>
    </ul>
    <button class="cart-btn">🛒 Panier (0)</button>
  </nav>

  <section class="search-section">
    <h2>Rechercher un produit</h2>
    <p class="search-subtitle">Trouvez le produit que vous cherchez</p>
    <div class="search-bar">
      <form method="GET" action="search.php" style="display:flex; gap:8px;">
        <input type="text" name="q" placeholder="Ex: laptop, smartphone..." value="<?php echo $search; ?>">
        <button type="submit">🔍 Rechercher</button>
      </form>
    </div>

    <!-- RÉSULTAT VULNÉRABLE XSS RÉFLÉCHI -->
    <?php if ($search != ''): ?>
      <div class="search-result">
        <!-- VOLONTAIREMENT VULNÉRABLE -->
        <p class="search-info">Résultats pour : <strong><?php echo $search; ?></strong></p>
        
        <?php if (count($results) > 0): ?>
          <div class="grid">
            <?php foreach ($results as $product): ?>
              <div