<?php
$search = '';
$results = [];

if (isset($_GET['q'])) {
  $search = $_GET['q'];
}

$products = [
  ['name' => 'Laptop Pro X', 'price' => '$799', 'img' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80'],
  ['name' => 'Gaming Laptop Z', 'price' => '$1299', 'img' => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80'],
  ['name' => 'Smartphone Z12', 'price' => '$499', 'img' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80'],
  ['name' => 'iPhone 15 Pro', 'price' => '$1099', 'img' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80'],
  ['name' => 'AirPods Pro 2', 'price' => '$249', 'img' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80'],
  ['name' => 'Sony WH-1000XM5', 'price' => '$349', 'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'],
  ['name' => 'Apple Watch S9', 'price' => '$399', 'img' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'],
  ['name' => 'PlayStation 5', 'price' => '$499', 'img' => 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80'],
  ['name' => 'Dell 27" 4K Monitor', 'price' => '$599', 'img' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80'],
  ['name' => 'Logitech MX Master', 'price' => '$99', 'img' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80'],
];

foreach ($products as $product) {
  if (stripos($product['name'], $search) !== false) {
    $results[] = $product;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop - Search</title>
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

    <!-- SEARCH -->
    <section class="search-section">
      <h2>Search Products</h2>
      <p class="search-subtitle">Find what you're looking for</p>

      <div class="search-bar">
        <form method="GET" action="search.php" style="display:flex; gap:8px;">
          <input type="text" name="q" placeholder="Ex: laptop, airpods..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit"><i class="fa fa-search"></i> Search</button>
        </form>
      </div>

      <?php if ($search != ''): ?>
        <div class="search-result">
          <!-- VOLONTAIREMENT VULNÉRABLE XSS -->
          <p class="search-info">Results for : <strong style="color:#a78bfa;"><?php echo $search; ?></strong></p>

          <?php if (count($results) > 0): ?>
            <div class="products-grid">
              <?php foreach ($results as $product): ?>
                <div class="product-card">
                  <img class="product-img" src="<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>">
                  <div class="product-info">
                    <div class="product-name"><?php echo $product['name']; ?></div>
                    <div class="product-price"><?php echo $product['price']; ?></div>
                    <div class="product-actions">
                      <button class="btn-cart">
                        <i class="fa fa-cart-plus"></i> Add
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="no-result">No products found for "<?php echo $search; ?>"</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>

    <footer class="footer">
      © 2025 HackTheShop — Educational Cybersecurity Project
    </footer>

  </div>

  <script src="../js/main.js"></script>
</body>
</html>