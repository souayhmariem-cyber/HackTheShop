<?php
$search  = '';
$results = [];

if (isset($_GET['q'])) {
  $search = trim($_GET['q']);
}

$products = [
  ['name' => 'Laptop Pro X',       'price' => '$799',  'img' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80'],
  ['name' => 'Gaming Laptop Z',    'price' => '$1299', 'img' => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80'],
  ['name' => 'Smartphone Z12',     'price' => '$499',  'img' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80'],
  ['name' => 'iPhone 15 Pro',      'price' => '$1099', 'img' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80'],
  ['name' => 'AirPods Pro 2',      'price' => '$249',  'img' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80'],
  ['name' => 'Sony WH-1000XM5',    'price' => '$349',  'img' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80'],
  ['name' => 'Apple Watch S9',     'price' => '$399',  'img' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80'],
  ['name' => 'PlayStation 5',      'price' => '$499',  'img' => 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80'],
  ['name' => 'Dell 27" 4K Monitor','price' => '$599',  'img' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80'],
  ['name' => 'Logitech MX Master', 'price' => '$99',   'img' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80'],
  ['name' => 'MacBook Air M2',     'price' => '$999',  'img' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&q=80'],
  ['name' => 'Samsung S24',        'price' => '$899',  'img' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80'],
  ['name' => 'Nintendo Switch',    'price' => '$299',  'img' => 'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=400&q=80'],
  ['name' => 'GoPro Hero 12',      'price' => '$399',  'img' => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80'],
];

if ($search !== '') {
  foreach ($products as $product) {
    if (stripos($product['name'], $search) !== false) {
      $results[] = $product;
    }
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

    <section class="search-section">
      <h2>Search Products</h2>
      <p class="search-subtitle">Find what you're looking for</p>
      <div class="search-bar">
        <form method="GET" action="search.php" style="display:flex; gap:8px;">
          <input type="text" name="q" placeholder="Ex: laptop, airpods..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
          <button type="submit"><i class="fa fa-search"></i> Search</button>
        </form>
      </div>

      <?php if ($search !== ''): ?>
        <div class="search-result">
          <p class="search-info">Results for: <strong style="color:#a78bfa;"><?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?></strong></p>
          <?php if (count($results) > 0): ?>
            <div class="products-grid">
              <?php foreach ($results as $product): ?>
                <div class="product-card">
                  <img class="product-img" src="<?php echo htmlspecialchars($product['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="product-price"><?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="product-actions">
                      <button class="btn-cart"><i class="fa fa-cart-plus"></i> Add</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="no-result">No products found for "<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>

    <footer class="footer">© 2025 HackTheShop — Your one-stop shop for the latest tech & electronics</footer>
  </div>
  <script src="../js/main.js"></script>
</body>
</html>