/* ═══════════════════════════════════
   PARTICLE BACKGROUND
   ═══════════════════════════════════ */
const canvas = document.getElementById('particles');
const ctx = canvas.getContext('2d');

function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

const particles = Array.from({ length: 150 }, () => ({
  x: Math.random() * canvas.width,
  y: Math.random() * canvas.height,
  r: Math.random() * 3 + 1,
  vx: (Math.random() - 0.5) * 0.35,
  vy: (Math.random() - 0.5) * 0.35,
  alpha: Math.random(),
  alphaDir: Math.random() > 0.5 ? 1 : -1,
  alphaSpeed: Math.random() * 0.008 + 0.002,
  color: Math.random() > 0.85
    ? (Math.random() > 0.5 ? '124,58,237' : '34,211,238')
    : '255,255,255'
}));

function drawParticles() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  for (let i = 0; i < particles.length; i++) {
    for (let j = i + 1; j < particles.length; j++) {
      const dx = particles[i].x - particles[j].x;
      const dy = particles[i].y - particles[j].y;
      const dist = Math.sqrt(dx * dx + dy * dy);
      if (dist < 100) {
        ctx.beginPath();
        ctx.moveTo(particles[i].x, particles[i].y);
        ctx.lineTo(particles[j].x, particles[j].y);
        ctx.strokeStyle = `rgba(124,58,237,${0.12 * (1 - dist / 100)})`;
        ctx.lineWidth = 0.4;
        ctx.stroke();
      }
    }
  }

  particles.forEach(p => {
    p.x += p.vx;
    p.y += p.vy;
    if (p.x < 0) p.x = canvas.width;
    if (p.x > canvas.width) p.x = 0;
    if (p.y < 0) p.y = canvas.height;
    if (p.y > canvas.height) p.y = 0;

    p.alpha += p.alphaDir * p.alphaSpeed;
    if (p.alpha >= 1) { p.alpha = 1; p.alphaDir = -1; }
    if (p.alpha <= 0.04) { p.alpha = 0.04; p.alphaDir = 1; }

    ctx.beginPath();
    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(${p.color},${p.alpha})`;
    ctx.fill();
  });

  requestAnimationFrame(drawParticles);
}
drawParticles();


/* ═══════════════════════════════════
   SIDEBAR
   ═══════════════════════════════════ */
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('active');
}


/* ═══════════════════════════════════
   SEARCH
   ═══════════════════════════════════ */
function toggleSearch() {
  document.getElementById('searchDropdown').classList.toggle('active');
}

function searchProducts() {
  const query = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(card => {
    const name = card.querySelector('.product-name').textContent.toLowerCase();
    card.style.display = name.includes(query) ? 'block' : 'none';
  });
}


/* ═══════════════════════════════════
   CART
   ═══════════════════════════════════ */
let cart = [];

function toggleCart() {
  document.getElementById('cartPanel').classList.toggle('open');
  document.getElementById('wishPanel').classList.remove('open');
}

function addToCart(name) {
  cart.push(name);
  document.getElementById('cartCount').textContent = cart.length;
  renderCart();
  document.getElementById('cartPanel').classList.add('open');
  document.getElementById('wishPanel').classList.remove('open');
}

function removeFromCart(index) {
  cart.splice(index, 1);
  document.getElementById('cartCount').textContent = cart.length;
  renderCart();
}

function renderCart() {
  const cartItems = document.getElementById('cartItems');
  if (cart.length === 0) {
    cartItems.innerHTML = '<p class="empty-msg">Your cart is empty.</p>';
    return;
  }
  cartItems.innerHTML = '';
  cart.forEach((item, index) => {
    const div = document.createElement('div');
    div.className = 'cart-item';
    div.innerHTML = `
      <span>${item}</span>
      <button onclick="removeFromCart(${index})" style="
        background: transparent;
        border: 1px solid #7c3aed;
        color: #f472b6;
        border-radius: 6px;
        padding: 4px 8px;
        cursor: pointer;
        font-size: 11px;
        font-family: 'Courier New', monospace;
      ">Remove</button>
    `;
    cartItems.appendChild(div);
  });
}


/* ═══════════════════════════════════
   WISHLIST
   ═══════════════════════════════════ */
let wishlist = [];

function toggleWishlist() {
  document.getElementById('wishPanel').classList.toggle('open');
  document.getElementById('cartPanel').classList.remove('open');
}

function toggleWish(btn) {
  const name = btn.closest('.product-card').querySelector('.product-name').textContent;
  btn.classList.toggle('active');

  if (btn.classList.contains('active')) {
    wishlist.push(name);
  } else {
    wishlist = wishlist.filter(i => i !== name);
  }

  const wishItems = document.getElementById('wishItems');
  if (wishlist.length === 0) {
    wishItems.innerHTML = '<p class="empty-msg">Your wishlist is empty.</p>';
  } else {
    wishItems.innerHTML = '';
    wishlist.forEach(item => {
      const div = document.createElement('div');
      div.className = 'cart-item';
      div.innerHTML = `<span>${item}</span><span style="color:#f472b6;">❤️</span>`;
      wishItems.appendChild(div);
    });
  }
}


/* ═══════════════════════════════════
   FILTER CATEGORIES
   ═══════════════════════════════════ */
function filterProducts(category, btn) {
  const cards = document.querySelectorAll('.product-card');
  const btns = document.querySelectorAll('.cat-btn');

  btns.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  cards.forEach(card => {
    if (category === 'all' || card.dataset.category === category) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}


/* ═══════════════════════════════════
   CHATBOT
   ═══════════════════════════════════ */
function toggleChatbot() {
  document.getElementById('chatbotFrame').classList.toggle('open');
}