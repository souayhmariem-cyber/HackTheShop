<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop - Order Tracker</title>
  <link rel="stylesheet" href="style.css">
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
        <a href="login.html"><i class="fa fa-user"></i> Login</a>
        <a href="promotions.html"><i class="fa fa-tag"></i> Promotions</a>
        <a href="account.php"><i class="fa fa-circle-user"></i> Your Account</a>
        <a href="#" onclick="toggleCSMenu(event)">
          <i class="fa fa-headset"></i> Customer Service
          <i class="fa fa-chevron-down" id="csChevron"></i>
        </a>
        <div class="sidebar-submenu" id="csSubmenu">
          <a href="chatbot.php"><i class="fa fa-comment"></i> Chat with Echo</a>
          <a href="customer-service.html"><i class="fa fa-envelope"></i> Contact Us</a>
        </div>
      </nav>
    </aside>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-left">
        <button class="burger" onclick="toggleSidebar()">
          <i class="fa fa-bars"></i>
        </button>
        <a href="login.html" class="nav-login">Login</a>
      </div>
      <div class="nav-center">
        <a href="index.html" style="text-decoration:none;">
          <span class="logo">HackTheShop</span>
        </a>
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

    <!-- SEARCH DROPDOWN -->
    <div class="search-dropdown" id="searchDropdown">
      <input type="text" id="searchInput" placeholder="Search products...">
      <button onclick="searchProducts()"><i class="fa fa-search"></i></button>
    </div>

    <!-- PAGE HEADER -->
    <div style="
      background: linear-gradient(135deg, #0d0d14 0%, #13131f 100%);
      border-bottom: 1px solid #3b2f6e;
      padding: 32px 24px 24px;
      text-align: center;
    ">
      <h1 style="color:#a78bfa; font-size:22px; letter-spacing:2px; margin-bottom:8px;">
        📦 Order Tracker
      </h1>
      <p style="color:#534AB7; font-size:13px; font-family:'Courier New',monospace;">
        Track your orders by ID
      </p>
      <div style="
        display: inline-block;
        margin-top: 16px;
        background: #1a0a00;
        border: 1px solid #f59e0b44;
        border-radius: 8px;
        padding: 8px 18px;
        font-family: 'Courier New', monospace;
        font-size: 11px;
        color: #f59e0b;
        letter-spacing: 1px;
      ">
        ⚠️ CHALLENGE — Can you access an order that isn't yours?
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <section style="
      max-width: 700px;
      margin: 0 auto;
      padding: 40px 24px;
      display: flex;
      flex-direction: column;
      gap: 24px;
    ">

      <!-- YOUR ORDERS -->
      <div style="
        background: #0d0d14;
        border: 1px solid #3b2f6e;
        border-radius: 14px;
        overflow: hidden;
      ">
        <div style="
          padding: 14px 20px;
          border-bottom: 1px solid #3b2f6e;
          display: flex;
          align-items: center;
          justify-content: space-between;
        ">
          <h2 style="color:#a78bfa; font-size:14px; letter-spacing:1px; margin:0;">
            🧾 Your Orders
          </h2>
          <span style="color:#534AB7; font-size:11px;">logged-in session</span>
        </div>
        <div style="padding:16px; display:flex; flex-direction:column; gap:10px;">
          <div class="modal-item">
            <span><i class="fa fa-box" style="color:#534AB7; margin-right:8px;"></i>#ORD-001 — Laptop Pro X</span>
            <span class="badge-delivered">Delivered</span>
          </div>
          <div class="modal-item">
            <span><i class="fa fa-box" style="color:#534AB7; margin-right:8px;"></i>#ORD-004 — PlayStation 5</span>
            <span class="badge-shipping">Shipping</span>
          </div>
          <div class="modal-item">
            <span><i class="fa fa-box" style="color:#534AB7; margin-right:8px;"></i>#ORD-010 — Sony WH-1000XM5</span>
            <span class="badge-shipping">Shipping</span>
          </div>
        </div>
      </div>

      <!-- IDOR TRACKER -->
      <div style="
        background: #0d0d14;
        border: 1px solid #3b2f6e;
        border-radius: 14px;
        overflow: hidden;
      ">
        <div style="
          padding: 14px 20px;
          border-bottom: 1px solid #3b2f6e;
          display: flex;
          align-items: center;
          justify-content: space-between;
        ">
          <h2 style="color:#a78bfa; font-size:14px; letter-spacing:1px; margin:0;">
            🔍 Order Tracker
          </h2>
          <span style="
            font-size:10px;
            color:#534AB7;
            letter-spacing:1px;
            font-family:'Courier New',monospace;
          ">Enter any order ID</span>
        </div>

        <div style="padding:20px;">

          <!-- Input row -->
          <div style="display:flex; gap:8px; margin-bottom:16px;">
            <input
              type="number"
              id="orderIdInput"
              placeholder="Try 1, 2, 3..."
              min="1"
              style="
                flex:1;
                background:#13131f;
                border:1px solid #3b2f6e;
                border-radius:25px;
                padding:10px 16px;
                color:#e2d9f3;
                font-family:'Courier New',monospace;
                font-size:13px;
                outline:none;
              "
            >
            <button onclick="fetchOrder()" style="
              background:#7c3aed;
              border:none;
              color:white;
              padding:10px 22px;
              border-radius:25px;
              cursor:pointer;
              font-family:'Courier New',monospace;
              font-size:13px;
              transition: background 0.2s;
            ">
              <i class="fa fa-search"></i> Track
            </button>
          </div>

          <!-- Quick-pick buttons -->
          <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
            <span style="color:#534AB7; font-size:11px; align-self:center;">Quick:</span>
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <button onclick="quickTrack(<?php echo $i; ?>)" style="
                background: transparent;
                border: 1px solid #3b2f6e;
                color: #a78bfa;
                padding: 4px 12px;
                border-radius: 20px;
                cursor: pointer;
                font-family: 'Courier New', monospace;
                font-size: 11px;
                transition: all 0.2s;
              " onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor='#3b2f6e'">
                #<?php echo $i; ?>
              </button>
            <?php endfor; ?>
          </div>

          <!-- Result box -->
          <div id="orderResult" style="min-height:40px; font-family:'Courier New',monospace; font-size:12px;"></div>

        </div>
      </div>

      <!-- EDUCATIONAL NOTE -->
      <div style="
        background: #0d0d14;
        border: 1px solid #3b2f6e;
        border-left: 3px solid #f59e0b;
        border-radius: 10px;
        padding: 18px 20px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
      ">
        <div style="color:#f59e0b; letter-spacing:1px; margin-bottom:10px;">
          🎓 HOW IDOR WORKS
        </div>
        <p style="color:#8b8ba0; line-height:1.7; margin:0;">
          <strong style="color:#e2d9f3;">Insecure Direct Object Reference</strong> happens when an app
          uses a user-supplied ID to fetch a resource without checking ownership.<br><br>
          This tracker calls <code style="color:#a78bfa;">order.php?id=X</code> with no server-side check
          that the order belongs to <em>you</em>. Changing the number gives you
          <strong style="color:#e94560;">anyone's order, card digits, and address</strong> — just by guessing sequential IDs.<br><br>
          <strong style="color:#22c55e;">Fix:</strong> always add
          <code style="color:#86efac;">AND user_id = $_SESSION['user_id']</code> to the query.
        </p>
      </div>

    </section>

    <!-- CART PANEL -->
    <div class="panel" id="cartPanel">
      <div class="panel-header">
        🛒 Cart
        <button onclick="toggleCart()">✕</button>
      </div>
      <div class="panel-body" id="cartItems">
        <p class="empty-msg">Your cart is empty.</p>
      </div>
    </div>

    <!-- WISHLIST PANEL -->
    <div class="panel" id="wishPanel">
      <div class="panel-header">
        ❤️ Wishlist
        <button onclick="toggleWishlist()">✕</button>
      </div>
      <div class="panel-body" id="wishItems">
        <p class="empty-msg">Your wishlist is empty.</p>
      </div>
    </div>

    <!-- CHATBOT BUBBLE -->
    <div class="chatbot-bubble" onclick="toggleChatbot()">
      <i class="fa fa-comment"></i>
    </div>
    <div class="chatbot-frame" id="chatbotFrame">
      <iframe src="chatbot.php" frameborder="0"></iframe>
    </div>

    <footer class="footer">
      © 2025 HackTheShop — Educational Cybersecurity Project
    </footer>

  </div>

  <script src="js/main.js"></script>
  <script src="popup.js"></script>
  <script>
    // Injected server-side — works because this file is .php
    window.__CURRENT_USER_ID = <?php echo intval($_SESSION['user_id'] ?? 0); ?>;
  </script>
  <script>
    function getCurrentUserId() {
      return window.__CURRENT_USER_ID || 0;
    }

    function quickTrack(id) {
      document.getElementById('orderIdInput').value = id;
      fetchOrder();
    }

    function fetchOrder() {
      const id     = document.getElementById('orderIdInput').value;
      const result = document.getElementById('orderResult');

      if (!id) {
        result.innerHTML = '<span style="color:#534AB7;">Enter an order ID.</span>';
        return;
      }

      result.innerHTML = '<span style="color:#534AB7;"><i class="fa fa-spinner fa-spin"></i> Loading...</span>';

      fetch('order.php?id=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(function(data) {

          if (data.error) {
            result.innerHTML = `
              <div style="color:#e94560; padding:12px; background:#1a0a0a;
                border-radius:8px; border:1px solid #e9456044;">
                ❌ ${data.error}
              </div>`;
            return;
          }

          const isOtherUser = getCurrentUserId() > 0 && data.user_id != getCurrentUserId();

          const statusColor = {
            delivered: '#22d3ee',
            shipping:  '#f59e0b',
            pending:   '#a78bfa',
            cancelled: '#e94560'
          }[data.status] || '#a78bfa';

          result.innerHTML = `
            <div style="
              background: #080810;
              border: 1px solid ${isOtherUser ? '#f59e0b' : '#3b2f6e'};
              border-radius: 10px;
              padding: 16px;
              transition: border-color 0.3s;
            ">
              ${isOtherUser ? `
                <div style="
                  color: #f59e0b;
                  font-size: 10px;
                  letter-spacing: 1px;
                  margin-bottom: 12px;
                  padding: 6px 10px;
                  background: #1a0a0044;
                  border-radius: 6px;
                  border: 1px solid #f59e0b44;
                ">
                  ⚠️ THIS ORDER BELONGS TO ANOTHER USER — IDOR SUCCESSFUL
                </div>
              ` : `
                <div style="
                  color: #22c55e;
                  font-size: 10px;
                  letter-spacing: 1px;
                  margin-bottom: 12px;
                ">✓ This is your order</div>
              `}

              <div style="display:grid; gap:8px;">
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">Order ID</span>
                  <span style="color:#22d3ee; font-weight:bold;">#${data.id}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">User ID</span>
                  <span style="color:${isOtherUser ? '#f59e0b' : '#e2d9f3'};">
                    ${data.user_id}
                    <span style="font-size:10px; margin-left:6px; opacity:0.7;">
                      ${isOtherUser ? '← not you!' : '← you'}
                    </span>
                  </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">Product</span>
                  <span style="color:#e2d9f3;">ID #${data.product_id ?? 'N/A'}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">Total</span>
                  <span style="color:#22c55e; font-weight:bold;">${data.total}€</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">Status</span>
                  <span style="color:${statusColor}; text-transform:uppercase; font-size:11px; letter-spacing:1px;">
                    ${data.status}
                  </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #1e1a2e;">
                  <span style="color:#534AB7;">Card</span>
                  <span style="color:#e94560;">
                    **** **** **** ${data.card_last4 ?? '????'}
                    ${isOtherUser ? '<span style="font-size:10px; margin-left:6px; color:#f59e0b;">← exposed!</span>' : ''}
                  </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0;">
                  <span style="color:#534AB7;">Date</span>
                  <span style="color:#534AB7; font-size:11px;">${data.created_at ?? '—'}</span>
                </div>
              </div>
            </div>
          `;

          if (isOtherUser) {
            setTimeout(function() {
              fetch('detector.php')
                .then(r => r.json())
                .then(function(res) {
                  if (res.detected) showAttackPopup(res.data);
                });
            }, 800);
          }

        })
        .catch(function() {
          result.innerHTML = `
            <div style="color:#e94560; padding:12px; background:#1a0a0a;
              border-radius:8px; border:1px solid #e9456044;">
              ❌ Could not reach order.php — make sure it exists.
            </div>`;
        });
    }

    document.getElementById('orderIdInput').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') fetchOrder();
    });

  </script>

</body>
</html>