<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop // Gamified Tech Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="text-slate-200">

<canvas id="particles"></canvas>

<!-- ── DRAW MODAL ──────────────────────────────────────────────────────── -->
<div id="drawModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-4 hidden">
  <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 w-full max-w-md text-center space-y-6 shadow-2xl relative overflow-hidden">
    <h3 class="text-xl font-bold text-white tracking-wide">🎰 Drawing Category Drop</h3>
    <p class="text-xs text-slate-400">Selecting fulfillment packages randomly...</p>
    <div class="flex justify-center py-6">
      <div class="relative w-44 h-44 flex items-center justify-center">
        <div id="draw-wheel" class="absolute inset-0 border-4 border-slate-800 border-dashed rounded-full bg-slate-950 flex items-center justify-center">
          <div class="w-36 h-36 rounded-full border-4 border-cyan-500/10 flex items-center justify-center text-slate-700 text-2xl flex-wrap">
            <i class="fa-solid fa-laptop p-2 opacity-20"></i>
            <i class="fa-solid fa-mobile-screen p-2 opacity-20"></i>
            <i class="fa-solid fa-headphones p-2 opacity-20"></i>
            <i class="fa-solid fa-gamepad p-2 opacity-20"></i>
          </div>
        </div>
        <div class="absolute -top-1 border-x-[10px] border-x-transparent border-t-[18px] border-t-cyan-500 z-10"></div>
        <div class="w-12 h-12 rounded-full bg-slate-950 border border-slate-800 flex items-center justify-center text-lg text-cyan-400 font-extrabold z-10 shadow-xl">
          <i class="fa-solid fa-dice animate-spin"></i>
        </div>
      </div>
    </div>
    <div id="draw-success" class="hidden border-t border-slate-800/60 pt-4 space-y-4 transition-all duration-500">
      <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">Reward Allocated</span>
      <h4 class="text-lg font-black text-white" id="draw-won-name"></h4>
      <img id="draw-won-img" src="" alt="" class="w-32 h-32 object-cover rounded-2xl mx-auto border border-slate-700">
      <p class="text-cyan-400 font-bold text-xl" id="draw-won-price"></p>
      <button onclick="closeDrawModal()" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition text-xs">
        ✓ Claim & Add to Inventory
      </button>
    </div>
  </div>
</div>

<!-- ── AUTH MODAL ──────────────────────────────────────────────────────── -->
<div id="authModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
  <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-sm space-y-4 shadow-2xl">
    <div class="text-center">
      <h3 class="text-lg font-bold text-white" id="auth-title">Account Gateway</h3>
      <p class="text-xs text-slate-400 mt-1" id="auth-subtitle">Verify user privileges.</p>
    </div>
    <div id="auth-error" class="hidden text-xs text-red-400 bg-red-900/20 border border-red-800/40 px-3 py-2 rounded-xl"></div>
    <div class="space-y-3">
      <input type="text" id="auth-user" placeholder="Username" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-cyan-500">
      <input type="email" id="auth-email" placeholder="Email (register only)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-cyan-500 hidden">
      <input type="password" id="auth-pass" placeholder="••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:border-cyan-500">
    </div>
    <button onclick="handleAuth()" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold rounded-xl transition text-sm" id="auth-submit-btn">Proceed</button>
    <div class="text-center text-xs text-slate-400">
      <button onclick="toggleAuthMode()" id="auth-toggle-btn" class="text-cyan-400 hover:underline">Switch to Register</button>
      <span class="mx-2 text-slate-700">|</span>
      <button onclick="closeAuth()" class="text-slate-500 hover:text-white">Cancel</button>
    </div>
  </div>
</div>

<!-- ── PRODUCT MODAL ────────────────────────────────────────────────────── -->
<div class="product-modal" id="productModal">
  <div class="product-modal-box">
    <button class="text-slate-400 hover:text-white absolute top-4 right-4 text-xl" onclick="closeProductModal()">✕</button>
    <div class="product-modal-content flex flex-col md:flex-row gap-6 items-center">
      <img id="modalImg" src="" alt="" class="product-modal-img w-full md:w-64 h-64 object-cover rounded-2xl border border-slate-800 shadow">
      <div class="product-modal-info flex-1 space-y-4">
        <h2 id="modalName" class="text-2xl font-bold text-white"></h2>
        <div id="modalPrice" class="product-modal-price text-xl font-bold text-cyan-400"></div>
        <div class="text-xs px-2.5 py-1 bg-slate-800 text-slate-400 rounded-md inline-block uppercase font-bold" id="modalCatTag"></div>
        <p id="modalDesc" class="product-modal-desc text-xs text-slate-400 leading-relaxed"></p>
        <div class="flex gap-2">
          <button class="flex-1 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold rounded-xl transition flex items-center justify-center gap-2 text-sm" id="modalAddCart" onclick="addModalToCart()">
            <i class="fa fa-cart-plus"></i> Add to Cart
          </button>
          <button class="py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-rose-400 rounded-xl transition text-sm" id="modalWishBtn" onclick="toggleModalWish()">
            <i class="fa fa-heart"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="product-modal-comments mt-8 border-t border-slate-800/60 pt-6">
      <h3 class="text-sm font-bold text-slate-300 flex items-center gap-2 mb-4"><i class="fa-solid fa-comments text-cyan-500"></i> Customer Reviews</h3>
      <div class="comments-list space-y-3 mb-6 max-h-48 overflow-y-auto" id="modalCommentsList"></div>
      <div class="comment-form bg-slate-950 border border-slate-800/40 p-4 rounded-xl space-y-3">
        <h4 class="text-xs font-bold text-slate-300">Leave a Review</h4>
        <input type="text" id="modalAuthor" placeholder="Your name" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:outline-none focus:border-cyan-500">
        <textarea id="modalText" rows="2" placeholder="Write your review..." class="w-full bg-slate-900 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-slate-100 focus:outline-none resize-none"></textarea>
        <button class="px-4 py-1.5 bg-cyan-700 hover:bg-cyan-600 text-white rounded-lg text-xs font-bold transition" onclick="submitModalComment()">Post Review</button>
      </div>
    </div>
  </div>
</div>

<!-- ── REWARDS INVENTORY MODAL ─────────────────────────────────────────── -->
<div id="rewardsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
  <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 w-full max-w-lg space-y-4 shadow-2xl max-h-[80vh] overflow-y-auto">
    <div class="flex justify-between items-center">
      <h3 class="text-lg font-bold text-white"><i class="fa fa-trophy text-yellow-400 mr-2"></i>My Rewards</h3>
      <button onclick="closeRewardsModal()" class="text-slate-500 hover:text-white">✕</button>
    </div>
    <div id="rewardsList" class="space-y-3"></div>
  </div>
</div>

<div class="wrapper min-h-screen flex flex-col justify-between">

  <!-- ── SIDEBAR ──────────────────────────────────────────────────────── -->
  <aside class="sidebar fixed top-0 left-[-300px] w-[260px] h-full bg-slate-950 border-r border-slate-800/60 transition-all duration-300 ease-in-out z-40 p-6 flex flex-col justify-between shadow-2xl" id="sidebar">
    <div class="space-y-8">
      <div class="sidebar-header flex justify-between items-center">
        <span class="text-base font-bold text-white tracking-wide"><i class="fa-solid fa-feather text-cyan-400 mr-2"></i> HackTheShop</span>
        <button class="text-slate-500 hover:text-white text-base" onclick="toggleSidebar()">✕</button>
      </div>
      <nav class="sidebar-nav flex flex-col gap-2">
        <button id="sidebar-auth-btn" onclick="openAuth()" class="w-full text-left text-xs font-bold text-slate-400 hover:text-white px-3 py-2 hover:bg-slate-900 rounded-lg"><i class="fa fa-user mr-2"></i> Login / Register</button>
        <button id="sidebar-rewards-btn" onclick="openRewardsModal()" class="hidden w-full text-left text-xs font-bold text-yellow-400 hover:text-yellow-300 px-3 py-2 hover:bg-slate-900 rounded-lg"><i class="fa fa-trophy mr-2"></i> My Rewards</button>
        <a href="javascript:void(0)" onclick="toggleCSMenu(event)" class="w-full text-left text-xs font-bold text-slate-400 hover:text-white px-3 py-2 hover:bg-slate-900 rounded-lg flex items-center justify-between">
          <span><i class="fa fa-headset mr-2"></i> Customer Support</span>
          <i class="fa fa-chevron-down text-[10px]" id="csChevron"></i>
        </a>
        <div class="hidden flex-col pl-6 gap-1" id="csSubmenu">
          <button onclick="toggleChatbot()" class="text-left text-xs text-slate-500 hover:text-cyan-400 py-1"><i class="fa fa-comment mr-2"></i> Chat with Echo</button>
        </div>
        <div id="sidebar-admin-link" class="hidden">
          <a href="admin/dashboard.php" class="w-full text-left text-xs font-bold text-red-500 hover:text-red-400 px-3 py-2 hover:bg-red-950/20 rounded-lg block"><i class="fa fa-user-gear mr-2"></i> Admin Panel</a>
        </div>
        <button id="sidebar-logout-btn" onclick="handleLogout()" class="hidden w-full text-left text-xs font-bold text-slate-400 hover:text-red-400 px-3 py-2 hover:bg-slate-900 rounded-lg"><i class="fa fa-right-from-bracket mr-2"></i> Logout</button>
      </nav>
    </div>
    <div class="text-[9px] text-slate-600 font-medium">Session integrity active</div>
  </aside>

  <div class="overlay fixed inset-0 bg-black/50 backdrop-blur-xs z-30 hidden" id="overlay" onclick="toggleSidebar()"></div>

  <!-- ── NAVBAR ────────────────────────────────────────────────────────── -->
  <nav class="navbar bg-slate-950/80 backdrop-blur-md sticky top-0 border-b border-slate-800/60 h-16 flex items-center px-4 md:px-12 justify-between z-30">
    <div class="nav-left flex items-center gap-4">
      <button class="burger text-slate-400 hover:text-white text-lg" onclick="toggleSidebar()">
        <i class="fa fa-bars"></i>
      </button>
      <button onclick="openAuth()" class="nav-login text-xs font-bold text-slate-300 hover:text-cyan-400" id="nav-login-btn">Login</button>
    </div>
    <div class="nav-center">
      <a href="javascript:void(0)" onclick="filterProducts('all', null)" class="text-lg font-black text-white tracking-wider flex items-center gap-2">
        <span class="text-cyan-400">HACK</span>THESHOP
      </a>
    </div>
    <div class="nav-right flex items-center gap-4">
      <button class="nav-icon text-slate-400 hover:text-cyan-400 text-sm" onclick="toggleSearch()"><i class="fa fa-search"></i></button>
      <button class="nav-icon text-slate-400 hover:text-rose-400 text-sm" onclick="toggleWishlist()"><i class="fa fa-heart"></i></button>
      <button class="nav-icon text-slate-400 hover:text-cyan-400 text-sm relative" onclick="toggleCart()">
        <i class="fa fa-shopping-cart"></i>
        <span class="cart-count absolute -top-1 -right-2 bg-cyan-600 text-[8px] text-slate-950 font-black rounded-full px-1 py-0.5" id="cartCount">0</span>
      </button>
    </div>
  </nav>

  <!-- ── SEARCH BAR ────────────────────────────────────────────────────── -->
  <div class="search-dropdown hidden bg-slate-900 border-b border-slate-800 p-3 max-w-xl mx-auto w-full rounded-b-2xl shadow-2xl relative z-20" id="searchDropdown">
    <div class="flex items-center gap-2">
      <input type="text" id="searchInput" placeholder="Search products..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-100 focus:outline-none focus:border-cyan-500" oninput="searchProducts()">
      <button onclick="searchProducts()" class="p-2 bg-cyan-600 text-slate-950 rounded-xl font-bold"><i class="fa fa-search"></i></button>
    </div>
  </div>

  <!-- ── HERO / CHALLENGE HEADER ──────────────────────────────────────── -->
  <div class="max-w-7xl mx-auto px-4 md:px-8 mt-6">
    <div class="bg-gradient-to-r from-slate-950 to-indigo-950 border border-slate-800 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
      <div class="absolute -right-12 -top-12 w-40 h-40 bg-violet-600/10 rounded-full blur-2xl"></div>
      <div class="space-y-2">
        <span class="text-[9px] font-bold text-cyan-400 uppercase tracking-widest bg-cyan-500/10 border border-cyan-500/20 px-3 py-1 rounded-full">Gamification Framework</span>
        <h2 class="text-xl font-black text-white">Unlock Divisions via HackerRank</h2>
        <p class="text-xs text-slate-400 max-w-lg">Complete SQL challenges to unlock categories and spin the reward wheel for free products.</p>
      </div>
      <div class="flex gap-3 text-xs" id="cat-action-area">
        <span class="text-slate-500 font-bold uppercase tracking-wide">Select a Category</span>
      </div>
    </div>
  </div>

  <!-- ── CATEGORIES ────────────────────────────────────────────────────── -->
  <div class="categories max-w-7xl mx-auto px-4 md:px-8 mt-6 flex flex-wrap gap-2" id="categoriesBar">
    <button class="cat-btn bg-slate-900 hover:bg-cyan-500/20 text-xs font-bold text-slate-300 hover:text-cyan-400 px-3 py-2 rounded-xl border border-slate-800 transition active" onclick="filterProducts('all', this)">All</button>
    <!-- Dynamic categories injected by JS -->
  </div>

  <!-- ── PRODUCTS GRID ─────────────────────────────────────────────────── -->
  <main class="max-w-7xl mx-auto px-4 md:px-8 mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pb-8" id="productsGrid">
    <div class="col-span-full flex justify-center py-12 text-slate-600 text-sm"><i class="fa fa-circle-notch fa-spin mr-2"></i> Loading products...</div>
  </main>

  <!-- ── CART PANEL ────────────────────────────────────────────────────── -->
  <div class="panel flex flex-col" id="cartPanel">
    <div class="p-4 bg-slate-950 border-b border-slate-800 flex justify-between items-center text-sm font-bold text-white">
      <span>🛒 Your Cart</span>
      <button onclick="toggleCart()" class="text-slate-500 hover:text-white">✕</button>
    </div>
    <div class="p-4 flex-1 overflow-y-auto space-y-3" id="cartItems">
      <p class="text-xs text-slate-500 text-center mt-8 empty-msg">Your cart is empty.</p>
    </div>
    <div class="p-4 bg-slate-950 border-t border-slate-800">
      <div class="flex justify-between items-center text-xs font-bold text-slate-300 mb-3">
        <span>Total:</span>
        <span class="text-cyan-400 text-sm" id="cartTotalValue">$0</span>
      </div>
      <button onclick="checkout()" class="w-full py-2 bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold rounded-xl text-xs transition">Order Dispatch</button>
    </div>
  </div>

  <!-- ── WISHLIST PANEL ────────────────────────────────────────────────── -->
  <div class="panel flex flex-col" id="wishPanel">
    <div class="p-4 bg-slate-950 border-b border-slate-800 flex justify-between items-center text-sm font-bold text-white">
      <span>❤️ Wishlist</span>
      <button onclick="toggleWishlist()" class="text-slate-500 hover:text-white">✕</button>
    </div>
    <div class="p-4 flex-1 overflow-y-auto space-y-3" id="wishItems">
      <p class="text-xs text-slate-500 text-center mt-8 empty-msg">Your wishlist is empty.</p>
    </div>
  </div>

  <!-- ── CHATBOT ───────────────────────────────────────────────────────── -->
  <div class="fixed bottom-6 right-6 z-40 flex flex-col items-end gap-3">
    <div class="hidden w-80 bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden flex-col shadow-2xl" id="chatbotFrame" style="height:400px;">
      <div class="bg-slate-900 border-b border-slate-800 p-4 flex justify-between items-center text-xs font-bold text-slate-200 shrink-0">
        <span><i class="fa-solid fa-robot text-cyan-400 mr-2"></i>Echo — AI Assistant</span>
        <button onclick="toggleChatbot()" class="text-slate-500 hover:text-white">✕</button>
      </div>
      <div class="flex-1 p-4 overflow-y-auto space-y-3 text-xs" id="chat-feed">
        <div class="bg-slate-900 p-3 rounded-2xl text-slate-300 max-w-[85%] leading-relaxed border border-slate-800">
          Hey! I'm Echo 👋 Ask me about products, challenges, or how the rewards system works.
        </div>
      </div>
      <div class="p-3 border-t border-slate-800 bg-slate-900/60 flex items-center gap-2 shrink-0">
        <input type="text" id="chatbotInput" placeholder="Write query..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none" onkeydown="if(event.key==='Enter') sendChatBot()">
        <button onclick="sendChatBot()" class="p-2 bg-cyan-600 hover:bg-cyan-500 text-slate-950 rounded-xl font-bold"><i class="fa-solid fa-paper-plane text-xs"></i></button>
      </div>
    </div>
    <button id="chatbot-bubble" onclick="toggleChatbot()" class="w-12 h-12 bg-gradient-to-tr from-cyan-500 to-indigo-600 rounded-full border-2 border-slate-800 text-white flex items-center justify-center text-lg hover:scale-110 transition shadow-2xl">
      <i class="fa-solid fa-robot"></i>
    </button>
  </div>

  <footer class="text-center py-6 border-t border-slate-800/40 text-[10px] text-slate-600 font-semibold tracking-wide">
    © 2026 HackTheShop — Gamified tech platform. All Rights Reserved.
  </footer>
</div>

<script>
/* ── STATE ──────────────────────────────────────────────────────────────── */
let state = {
  session: null,
  role: null,
  solved: [],
  products: [],
  categories: [],
  openedProduct: null
};

let activeCategory = 'all';
let authMode = 'login';

/* ── BOOT ───────────────────────────────────────────────────────────────── */
window.addEventListener('DOMContentLoaded', async () => {
  initParticles();
  await checkSession();
  await Promise.all([loadCategories(), loadProducts()]);
});

/* ── PARTICLES ──────────────────────────────────────────────────────────── */
function initParticles() {
  const canvas = document.getElementById('particles');
  const ctx = canvas.getContext('2d');
  const resize = () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight; };
  resize();
  window.addEventListener('resize', resize);
  let pts = Array.from({length: 40}, () => ({
    x: Math.random() * canvas.width, y: Math.random() * canvas.height,
    vx: Math.random() * 0.4 - 0.2, vy: Math.random() * 0.4 - 0.2,
    r: Math.random() * 2
  }));
  (function draw() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle = 'rgba(6,182,212,0.15)';
    pts.forEach(p => {
      ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI*2); ctx.fill();
      p.x += p.vx; p.y += p.vy;
      if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
      if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
    });
    requestAnimationFrame(draw);
  })();
}

/* ── AUTH ───────────────────────────────────────────────────────────────── */
async function checkSession() {
  const fd = new FormData(); fd.append('action','status');
  const res = await fetch('php/controllers/AuthController.php', {method:'POST', body:fd}).then(r=>r.json()).catch(()=>null);
  if (res?.ok && res.data.logged_in) {
    state.session = res.data.username;
    state.role = res.data.role;
    updateAuthUI();
    await loadProgress();
    loadCart();
    loadWishlist();
  }
}

function openAuth() {
  document.getElementById('authModal').classList.replace('hidden','flex');
  document.getElementById('auth-error').classList.add('hidden');
}
function closeAuth() {
  document.getElementById('authModal').classList.replace('flex','hidden');
}
function toggleAuthMode() {
  const btn = document.getElementById('auth-toggle-btn');
  const emailField = document.getElementById('auth-email');
  const title = document.getElementById('auth-title');
  const subtitle = document.getElementById('auth-subtitle');
  const submitBtn = document.getElementById('auth-submit-btn');
  if (authMode === 'login') {
    authMode = 'register';
    btn.innerText = 'Switch to Login';
    emailField.classList.remove('hidden');
    title.innerText = 'Create Account';
    subtitle.innerText = 'Join the platform.';
    submitBtn.innerText = 'Register';
  } else {
    authMode = 'login';
    btn.innerText = 'Switch to Register';
    emailField.classList.add('hidden');
    title.innerText = 'Account Gateway';
    subtitle.innerText = 'Verify user privileges.';
    submitBtn.innerText = 'Proceed';
  }
}
async function handleAuth() {
  const u = document.getElementById('auth-user').value.trim();
  const p = document.getElementById('auth-pass').value.trim();
  const e = document.getElementById('auth-email').value.trim();
  const errBox = document.getElementById('auth-error');
  errBox.classList.add('hidden');

  const fd = new FormData();
  fd.append('action', authMode);
  fd.append('username', u);
  fd.append('password', p);
  if (authMode === 'register') fd.append('email', e);

  const res = await fetch('php/controllers/AuthController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (!res.ok) {
    errBox.innerText = res.error;
    errBox.classList.remove('hidden');
    return;
  }
  state.session = res.data.username;
  state.role = res.data.role;
  closeAuth();
  updateAuthUI();
  await loadProgress();
  loadCart();
  loadWishlist();
  filterProducts(activeCategory, null);
}
async function handleLogout() {
  const fd = new FormData(); fd.append('action','logout');
  await fetch('php/controllers/AuthController.php', {method:'POST', body:fd});
  state.session = null; state.role = null; state.solved = [];
  updateAuthUI();
  filterProducts('all', null);
  updateCartUI([]);
  updateWishUI([]);
}
function updateAuthUI() {
  const loginBtn = document.getElementById('nav-login-btn');
  const sidebarBtn = document.getElementById('sidebar-auth-btn');
  const logoutBtn = document.getElementById('sidebar-logout-btn');
  const rewardsBtn = document.getElementById('sidebar-rewards-btn');
  const adminLink = document.getElementById('sidebar-admin-link');
  if (state.session) {
    loginBtn.innerText = `Hi, ${state.session}`;
    loginBtn.onclick = () => {};
    sidebarBtn.classList.add('hidden');
    logoutBtn.classList.remove('hidden');
    rewardsBtn.classList.remove('hidden');
    if (state.role === 'admin') adminLink.classList.remove('hidden');
  } else {
    loginBtn.innerText = 'Login';
    loginBtn.onclick = openAuth;
    sidebarBtn.classList.remove('hidden');
    logoutBtn.classList.add('hidden');
    rewardsBtn.classList.add('hidden');
    adminLink.classList.add('hidden');
  }
}

/* ── CATEGORIES ─────────────────────────────────────────────────────────── */
async function loadCategories() {
  const res = await fetch('php/controllers/ProductController.php?action=categories').then(r=>r.json());
  if (!res.ok) return;
  state.categories = res.data;
  const bar = document.getElementById('categoriesBar');
  res.data.forEach(c => {
    const btn = document.createElement('button');
    btn.className = 'cat-btn bg-slate-900 hover:bg-cyan-500/20 text-xs font-bold text-slate-300 hover:text-cyan-400 px-3 py-2 rounded-xl border border-slate-800 transition';
    btn.innerHTML = `<i class="fa ${c.icon} mr-1.5"></i> ${c.name}`;
    btn.onclick = () => filterProducts(c.slug, btn);
    bar.appendChild(btn);
  });
}

/* ── PRODUCTS ────────────────────────────────────────────────────────────── */
async function loadProducts(cat = 'all') {
  const res = await fetch(`php/controllers/ProductController.php?action=list&category=${cat}`).then(r=>r.json());
  if (!res.ok) return;
  state.products = res.data;
  renderProducts(res.data);
}

function renderProducts(prods) {
  const grid = document.getElementById('productsGrid');
  grid.innerHTML = '';
  if (!prods.length) {
    grid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-600 text-sm">No products found.</div>';
    return;
  }
  prods.forEach(p => {
    grid.innerHTML += `
      <div class="product-card bg-slate-950 border border-slate-800 p-4 rounded-2xl cursor-pointer hover:border-slate-700 transition duration-300 relative overflow-hidden shadow group" onclick="openProductModal(${p.id})">
        <img class="product-img w-full h-40 object-cover rounded-xl" src="${p.image_url}" alt="${p.name}" loading="lazy">
        <div class="product-info mt-4">
          <div class="product-name text-sm font-bold text-slate-200">${p.name}</div>
          <div class="text-[10px] text-slate-500 mt-0.5">${p.cat_name}</div>
          <div class="product-price text-cyan-400 font-black mt-1">$${parseFloat(p.price).toFixed(2)}</div>
          <div class="product-actions flex justify-between items-center mt-3 border-t border-slate-900 pt-2">
            <button class="btn-cart px-3 py-1 bg-slate-900 hover:bg-cyan-500/10 text-cyan-400 font-bold rounded-xl border border-slate-800 text-xs flex items-center gap-1 transition" onclick="event.stopPropagation(); addToCart(${p.id})"><i class="fa fa-cart-plus"></i> Add</button>
            <button class="btn-wish text-slate-500 hover:text-rose-400 text-xs transition" id="wish-btn-${p.id}" onclick="event.stopPropagation(); toggleWish(${p.id})"><i class="fa fa-heart"></i></button>
          </div>
        </div>
      </div>
    `;
  });
}

function filterProducts(cat, el) {
  activeCategory = cat;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('bg-cyan-500/20','text-cyan-400'));
  if (el) el.classList.add('bg-cyan-500/20','text-cyan-400');

  const actionArea = document.getElementById('cat-action-area');
  if (cat === 'all') {
    actionArea.innerHTML = `<span class="text-slate-500 font-bold uppercase tracking-wide text-xs">Select a Category</span>`;
    loadProducts('all');
    return;
  }

  const catObj = state.categories.find(c => c.slug === cat);
  if (!catObj) { loadProducts(cat); return; }
  const isSolved = state.solved.includes(cat);

  if (isSolved) {
    actionArea.innerHTML = `
      <button onclick="triggerRewardDraw()" class="px-3 py-1.5 bg-violet-600 hover:bg-violet-500 text-slate-100 font-bold rounded-xl flex items-center gap-1.5 shadow text-xs">
        🎰 Claim Loot Spin
      </button>`;
  } else {
    actionArea.innerHTML = `
      <a href="${catObj.challenge_url}" target="_blank" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold rounded-xl flex items-center gap-1.5 shadow text-xs">
        <i class="fa fa-arrow-up-right-from-square"></i> Unlock Challenge
      </a>
      <button onclick="claimSolve('${cat}')" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-emerald-400 border border-slate-800 font-bold rounded-xl flex items-center gap-1.5 shadow text-xs">
        <i class="fa fa-check"></i> I solved it
      </button>`;
  }
  loadProducts(cat);
}

/* ── PROGRESS & REWARDS ──────────────────────────────────────────────────── */
async function loadProgress() {
  if (!state.session) return;
  const fd = new FormData(); fd.append('action','get_progress');
  const res = await fetch('php/controllers/RewardController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) state.solved = res.data;
}

async function claimSolve(cat) {
  if (!state.session) { openAuth(); return; }
  if (state.solved.includes(cat)) return;
  const fd = new FormData(); fd.append('action','claim_solve'); fd.append('category', cat);
  const res = await fetch('php/controllers/RewardController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) {
    state.solved.push(cat);
    confetti({ particleCount: 60, spread: 70 });
    filterProducts(cat, null);
  }
}

async function triggerRewardDraw() {
  if (!state.session) { openAuth(); return; }
  const modal = document.getElementById('drawModal');
  const wheel = document.getElementById('draw-wheel');
  const success = document.getElementById('draw-success');
  modal.classList.remove('hidden');
  success.classList.add('hidden');
  wheel.className = 'absolute inset-0 border-4 border-cyan-500 rounded-full bg-slate-950 border-dashed flex items-center justify-center';
  wheel.style.animation = 'wheelSpin 4s cubic-bezier(0.25,0.1,0.25,1) forwards';

  const fd = new FormData(); fd.append('action','trigger_draw'); fd.append('category', activeCategory);
  const res = await fetch('php/controllers/RewardController.php', {method:'POST', body:fd}).then(r=>r.json());

  setTimeout(() => {
    wheel.style.animation = '';
    wheel.className = 'absolute inset-0 border-4 border-slate-800 border-dashed rounded-full bg-slate-950 flex items-center justify-center';
    if (res.ok) {
      const p = res.data.product;
      document.getElementById('draw-won-name').innerText = p.name;
      document.getElementById('draw-won-price').innerText = `$${parseFloat(p.price).toFixed(2)}`;
      document.getElementById('draw-won-img').src = p.image_url;
      success.classList.remove('hidden');
      confetti({ particleCount: 80, spread: 90 });
    } else {
      success.classList.remove('hidden');
      document.getElementById('draw-won-name').innerText = res.error || 'Error occurred.';
    }
  }, 4000);
}

function closeDrawModal() {
  document.getElementById('drawModal').classList.add('hidden');
  document.getElementById('draw-success').classList.add('hidden');
}

async function openRewardsModal() {
  document.getElementById('rewardsModal').classList.replace('hidden','flex');
  const list = document.getElementById('rewardsList');
  list.innerHTML = '<div class="text-xs text-slate-500 text-center py-4"><i class="fa fa-circle-notch fa-spin mr-2"></i>Loading...</div>';
  const fd = new FormData(); fd.append('action','get_rewards');
  const res = await fetch('php/controllers/RewardController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (!res.ok || !res.data.length) { list.innerHTML = '<div class="text-xs text-slate-500 text-center py-4">No rewards yet. Complete challenges to win products!</div>'; return; }
  list.innerHTML = res.data.map(r => `
    <div class="flex items-center gap-3 bg-slate-950 border border-slate-800 rounded-xl p-3">
      <img src="${r.image_url}" alt="${r.name}" class="w-14 h-14 object-cover rounded-xl border border-slate-700">
      <div>
        <div class="text-sm font-bold text-white">${r.name}</div>
        <div class="text-[10px] text-slate-400">${r.category}</div>
        <div class="text-cyan-400 font-bold text-xs mt-0.5">$${parseFloat(r.price).toFixed(2)}</div>
      </div>
      <span class="ml-auto text-[9px] text-emerald-400 bg-emerald-900/20 border border-emerald-800/30 px-2 py-0.5 rounded-full font-bold">WON</span>
    </div>
  `).join('');
}

function closeRewardsModal() {
  document.getElementById('rewardsModal').classList.replace('flex','hidden');
}

/* ── PRODUCT MODAL ───────────────────────────────────────────────────────── */
async function openProductModal(id) {
  const p = state.products.find(x => x.id == id);
  if (!p) return;
  state.openedProduct = p;
  document.getElementById('productModal').classList.add('active');
  document.getElementById('modalImg').src = p.image_url;
  document.getElementById('modalName').innerText = p.name;
  document.getElementById('modalPrice').innerText = `$${parseFloat(p.price).toFixed(2)}`;
  document.getElementById('modalCatTag').innerText = p.cat_name;
  document.getElementById('modalDesc').innerText = p.description || 'Premium quality tech product.';
  loadReviews(id);
}
function closeProductModal() { document.getElementById('productModal').classList.remove('active'); }
function addModalToCart() { if (state.openedProduct) addToCart(state.openedProduct.id); }
async function toggleModalWish() { if (state.openedProduct) await toggleWish(state.openedProduct.id); }

async function loadReviews(productId) {
  const list = document.getElementById('modalCommentsList');
  list.innerHTML = '<div class="text-xs text-slate-600">Loading...</div>';
  const res = await fetch(`php/controllers/ProductController.php?action=reviews&product_id=${productId}`).then(r=>r.json());
  if (!res.ok || !res.data.length) { list.innerHTML = '<p class="text-xs text-slate-500">No reviews yet. Be the first!</p>'; return; }
  list.innerHTML = res.data.map(r => `
    <div class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-xs text-slate-300">
      <span class="font-bold text-cyan-400">${r.author_name}:</span> ${r.review_text}
      <div class="text-[10px] text-slate-600 mt-1">${new Date(r.created_at).toLocaleDateString()}</div>
    </div>
  `).join('');
}

async function submitModalComment() {
  const author = document.getElementById('modalAuthor').value.trim();
  const text   = document.getElementById('modalText').value.trim();
  if (!author || !text || !state.openedProduct) return;
  const fd = new FormData();
  fd.append('action','add_review');
  fd.append('product_id', state.openedProduct.id);
  fd.append('author', author);
  fd.append('text', text);
  const res = await fetch('php/controllers/ProductController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) {
    document.getElementById('modalAuthor').value = '';
    document.getElementById('modalText').value = '';
    loadReviews(state.openedProduct.id);
  }
}

/* ── CART ────────────────────────────────────────────────────────────────── */
async function addToCart(productId) {
  if (!state.session) { openAuth(); return; }
  const fd = new FormData(); fd.append('action','add_cart'); fd.append('product_id', productId);
  const res = await fetch('php/controllers/ProductController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) {
    loadCart();
    // Pulse animation on cart icon
    const cartIcon = document.querySelector('[onclick="toggleCart()"] i');
    cartIcon.classList.add('text-cyan-400');
    setTimeout(() => cartIcon.classList.remove('text-cyan-400'), 600);
  }
}

async function loadCart() {
  if (!state.session) return;
  const res = await fetch('php/controllers/ProductController.php?action=get_cart').then(r=>r.json());
  if (res.ok) updateCartUI(res.data);
}

function updateCartUI(items) {
  document.getElementById('cartCount').innerText = items.reduce((s,i) => s + i.quantity, 0);
  const feed = document.getElementById('cartItems');
  const total = document.getElementById('cartTotalValue');
  if (!items.length) {
    feed.innerHTML = '<p class="text-xs text-slate-500 text-center mt-8">Your cart is empty.</p>';
    total.innerText = '$0';
    return;
  }
  let sum = 0;
  feed.innerHTML = items.map(p => {
    sum += p.price * p.quantity;
    return `
      <div class="p-2 bg-slate-900 rounded-xl flex justify-between items-center text-xs text-slate-200 gap-2">
        <img src="${p.image_url}" class="w-10 h-10 object-cover rounded-lg border border-slate-700">
        <div class="flex-1">
          <div class="font-bold">${p.name}</div>
          <div class="text-slate-500">x${p.quantity}</div>
        </div>
        <span class="text-cyan-400 font-bold">$${(p.price * p.quantity).toFixed(2)}</span>
        <button onclick="removeFromCart(${p.id})" class="text-slate-600 hover:text-red-400 transition">✕</button>
      </div>`;
  }).join('');
  total.innerText = `$${sum.toFixed(2)}`;
}

async function removeFromCart(productId) {
  const fd = new FormData(); fd.append('action','remove_cart'); fd.append('product_id', productId);
  await fetch('php/controllers/ProductController.php', {method:'POST', body:fd});
  loadCart();
}

async function checkout() {
  if (!state.session) { openAuth(); return; }
  const fd = new FormData(); fd.append('action','checkout');
  const res = await fetch('php/controllers/ProductController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) {
    confetti({ particleCount: 100, spread: 80 });
    alert(`✅ Order #${res.data.order_id} placed! Total: $${parseFloat(res.data.total).toFixed(2)}`);
    loadCart();
  } else {
    alert(res.error || 'Checkout failed.');
  }
}

/* ── WISHLIST ────────────────────────────────────────────────────────────── */
async function toggleWish(productId) {
  if (!state.session) { openAuth(); return; }
  const fd = new FormData(); fd.append('action','add_wish'); fd.append('product_id', productId);
  const res = await fetch('php/controllers/ProductController.php', {method:'POST', body:fd}).then(r=>r.json());
  if (res.ok) {
    const btn = document.getElementById(`wish-btn-${productId}`);
    if (btn) btn.classList.toggle('text-rose-400', res.data.wishlisted);
    loadWishlist();
  }
}

async function loadWishlist() {
  if (!state.session) return;
  const res = await fetch('php/controllers/ProductController.php?action=get_wish').then(r=>r.json());
  if (res.ok) updateWishUI(res.data);
}

function updateWishUI(items) {
  const feed = document.getElementById('wishItems');
  if (!items.length) { feed.innerHTML = '<p class="text-xs text-slate-500 text-center mt-8">Your wishlist is empty.</p>'; return; }
  feed.innerHTML = items.map(p => `
    <div class="p-2 bg-slate-900 rounded-xl flex gap-3 items-center text-xs text-slate-200">
      <img src="${p.image_url}" class="w-10 h-10 object-cover rounded-lg border border-slate-700">
      <div class="flex-1">
        <div class="font-bold">${p.name}</div>
        <div class="text-cyan-400 font-bold">$${parseFloat(p.price).toFixed(2)}</div>
      </div>
      <button onclick="addToCart(${p.id})" class="text-xs text-cyan-400 hover:text-cyan-300 transition"><i class="fa fa-cart-plus"></i></button>
    </div>`).join('');
}

/* ── PANELS ──────────────────────────────────────────────────────────────── */
function toggleCart() { document.getElementById('cartPanel').classList.toggle('active'); document.getElementById('wishPanel').classList.remove('active'); }
function toggleWishlist() { document.getElementById('wishPanel').classList.toggle('active'); document.getElementById('cartPanel').classList.remove('active'); }
function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const ol = document.getElementById('overlay');
  if (sb.style.left === '0px') { sb.style.left = '-300px'; ol.classList.add('hidden'); }
  else { sb.style.left = '0px'; ol.classList.remove('hidden'); }
}
function toggleCSMenu(e) { e.preventDefault(); document.getElementById('csSubmenu').classList.toggle('hidden'); }

/* ── SEARCH ──────────────────────────────────────────────────────────────── */
function toggleSearch() { document.getElementById('searchDropdown').classList.toggle('hidden'); }
function searchProducts() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  if (!q) { renderProducts(state.products); return; }
  renderProducts(state.products.filter(p => p.name.toLowerCase().includes(q) || p.cat_name.toLowerCase().includes(q)));
}

/* ── CHATBOT ─────────────────────────────────────────────────────────────── */
function toggleChatbot() {
  const frame = document.getElementById('chatbotFrame');
  frame.classList.toggle('hidden');
  frame.classList.toggle('flex');
}
async function sendChatBot() {
  const input = document.getElementById('chatbotInput');
  const feed  = document.getElementById('chat-feed');
  const msg   = input.value.trim();
  if (!msg) return;
  input.value = '';
  feed.innerHTML += `<div class="bg-slate-800 p-2.5 rounded-2xl text-slate-300 max-w-[85%] self-end text-right ml-auto">${msg}</div>`;
  feed.scrollTop = feed.scrollHeight;

  // Typing indicator
  const typingId = 'typing-' + Date.now();
  feed.innerHTML += `<div id="${typingId}" class="bg-slate-900 border border-slate-800 p-2.5 rounded-2xl text-slate-500 max-w-[85%] text-xs italic">Echo is typing…</div>`;
  feed.scrollTop = feed.scrollHeight;

  const fd = new FormData(); fd.append('message', msg);
  const res = await fetch('chatbot/reply.php', {method:'POST', body:fd}).then(r=>r.json()).catch(()=>({reply:'Connection error.'}));
  document.getElementById(typingId)?.remove();
  feed.innerHTML += `<div class="bg-slate-900 border border-slate-800 p-2.5 rounded-2xl text-slate-300 max-w-[85%] text-xs leading-relaxed">${res.reply.replace(/\n/g,'<br>').replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')}</div>`;
  feed.scrollTop = feed.scrollHeight;
}

/* ── CSS INJECTION for wheel spin ───────────────────────────────────────── */
const style = document.createElement('style');
style.textContent = `
@keyframes wheelSpin { 0%{transform:rotate(0deg)} 100%{transform:rotate(3600deg)} }
.product-modal.active { display:flex; }
.panel.active { right:0!important; }
`;
document.head.appendChild(style);
</script>
</body>
</html>