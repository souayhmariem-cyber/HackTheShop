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


/* ═══════════════════════════════════
   CUSTOMER SERVICE SUBMENU
   ═══════════════════════════════════ */
function toggleCSMenu(e) {
  e.preventDefault();
  document.getElementById('csSubmenu').classList.toggle('open');
  document.getElementById('csChevron').classList.toggle('fa-chevron-down');
  document.getElementById('csChevron').classList.toggle('fa-chevron-up');
}

/* ═══════════════════════════════════
   PRODUCT MODAL
   ═══════════════════════════════════ */
const products = {
  'Laptop Pro X': { price: '$799', img: 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80', desc: 'Powerful laptop with Intel Core i7, 16GB RAM, 512GB SSD. Perfect for work and gaming.' },
  'Gaming Laptop Z': { price: '$1299', img: 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=80', desc: 'High performance gaming laptop with RTX 4060, 32GB RAM, 1TB SSD.' },
  'Desktop Ultra': { price: '$1499', img: 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=400&q=80', desc: 'Ultimate desktop for professionals. Intel i9, 64GB RAM, 2TB SSD.' },
  'MacBook Air M2': { price: '$999', img: 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&q=80', desc: 'Ultra-thin and lightweight. Apple M2 chip, 8GB RAM, 256GB SSD.' },
  'Mini PC Pro': { price: '$499', img: 'https://images.unsplash.com/photo-1611078489935-0cb964de46d6?w=400&q=80', desc: 'Compact and powerful mini PC. Intel i5, 8GB RAM, 256GB SSD.' },
  'Smartphone Z12': { price: '$499', img: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80', desc: '6.5" AMOLED display, 108MP camera, 5000mAh battery.' },
  'iPhone 15 Pro': { price: '$1099', img: 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&q=80', desc: 'Apple iPhone 15 Pro. A17 Pro chip, 48MP camera, titanium design.' },
  'Samsung S24': { price: '$899', img: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80', desc: 'Samsung Galaxy S24. Snapdragon 8 Gen 3, 50MP camera, 4000mAh.' },
  'iPad Pro 12': { price: '$799', img: 'https://images.unsplash.com/photo-1544866092-1935c5ef2a8f?w=400&q=80', desc: 'Apple iPad Pro 12.9". M2 chip, Liquid Retina XDR display.' },
  'Google Pixel 8': { price: '$699', img: 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=400&q=80', desc: 'Google Pixel 8. Tensor G3 chip, 50MP camera, 7 years of updates.' },
  'AirPods Pro 2': { price: '$249', img: 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&q=80', desc: 'Apple AirPods Pro 2nd gen. Active Noise Cancellation, 30hr battery.' },
  'Sony WH-1000XM5': { price: '$349', img: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', desc: 'Industry-leading noise cancellation. 30hr battery, multipoint connection.' },
  'JBL Charge 5': { price: '$179', img: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&q=80', desc: 'Portable Bluetooth speaker. IP67 waterproof, 20hr battery.' },
  'Galaxy Buds Pro': { price: '$199', img: 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&q=80', desc: 'Samsung Galaxy Buds Pro. ANC, 28hr battery with case.' },
  'Sonos Soundbar': { price: '$449', img: 'https://images.unsplash.com/photo-1558089687-f282ffcbc126?w=400&q=80', desc: 'Premium soundbar with Dolby Atmos, WiFi, and voice control.' },
  'Apple Watch S9': { price: '$399', img: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80', desc: 'Apple Watch Series 9. Health tracking, GPS, 18hr battery.' },
  'Fitbit Charge 6': { price: '$159', img: 'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?w=400&q=80', desc: 'Advanced fitness tracker. Heart rate, GPS, sleep tracking.' },
  'Galaxy Watch 6': { price: '$299', img: 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=400&q=80', desc: 'Samsung Galaxy Watch 6. Health monitoring, 40hr battery.' },
  'Garmin Fenix 7': { price: '$599', img: 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?w=400&q=80', desc: 'Premium multisport GPS watch. Solar charging, 18-day battery.' },
  'Xiaomi Band 8': { price: '$49', img: 'https://images.unsplash.com/photo-1544117519-31a4b719223d?w=400&q=80', desc: 'Affordable fitness band. 16-day battery, heart rate, SpO2.' },
  'Sony Alpha A7': { price: '$2499', img: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80', desc: 'Full-frame mirrorless camera. 33MP sensor, 4K video, 5-axis stabilization.' },
  'Canon EOS R50': { price: '$799', img: 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=400&q=80', desc: 'Entry-level mirrorless camera. 24MP sensor, 4K video, dual pixel AF.' },
  'GoPro Hero 12': { price: '$399', img: 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80', desc: 'Action camera. 5.3K video, HyperSmooth 6.0, waterproof to 10m.' },
  'Polaroid Now+': { price: '$149', img: 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=400&q=80', desc: 'Instant film camera with Bluetooth. 5 creative lens filters.' },
  'DJI Mini 4 Pro': { price: '$759', img: 'https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=400&q=80', desc: '4K HDR drone. 34min flight time, obstacle sensing, under 249g.' },
  'PlayStation 5': { price: '$499', img: 'https://images.unsplash.com/photo-1593118247619-e2d6f056869e?w=400&q=80', desc: 'Sony PlayStation 5. 4K gaming, SSD storage, DualSense controller.' },
  'Xbox Controller': { price: '$59', img: 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=400&q=80', desc: 'Xbox Wireless Controller. Bluetooth, USB-C, textured grip.' },
  'Gaming Keyboard': { price: '$129', img: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&q=80', desc: 'Mechanical gaming keyboard. RGB backlight, tactile switches.' },
  'Gaming Mouse Pro': { price: '$79', img: 'https://images.unsplash.com/photo-1580327344181-c1163234e5a0?w=400&q=80', desc: 'High precision gaming mouse. 25K DPI, 11 programmable buttons.' },
  'Nintendo Switch': { price: '$299', img: 'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=400&q=80', desc: 'Hybrid gaming console. Play at home or on the go. 6hr battery.' },
  'Logitech MX Master': { price: '$99', img: 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80', desc: 'Advanced wireless mouse. MagSpeed scroll, ergonomic design.' },
  'USB-C Hub Pro': { price: '$49', img: 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&q=80', desc: '7-in-1 USB-C hub. 4K HDMI, 100W PD, SD card reader.' },
  'Mechanical Keyboard': { price: '$89', img: 'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=400&q=80', desc: 'Compact mechanical keyboard. Brown switches, RGB backlight.' },
  'Webcam 4K Pro': { price: '$129', img: 'https://images.unsplash.com/photo-1609081219090-a6d81d3085bf?w=400&q=80', desc: '4K webcam with autofocus. Built-in mic, privacy cover.' },
  'Power Bank 20000': { price: '$39', img: 'https://images.unsplash.com/photo-1591370874773-6702e8f12fd8?w=400&q=80', desc: '20000mAh power bank. 65W fast charging, 3 ports.' },
  'Dell 27" 4K Monitor': { price: '$599', img: 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80', desc: '27" 4K IPS monitor. USB-C, 99% sRGB, height adjustable.' },
  'Samsung Smart TV 55"': { price: '$799', img: 'https://images.unsplash.com/photo-1593359677879-a4bb92f4834c?w=400&q=80', desc: '55" 4K QLED Smart TV. Quantum HDR, Tizen OS, gaming mode.' },
  'LG Curved 34"': { price: '$699', img: 'https://images.unsplash.com/photo-1616763355548-1b606f439f86?w=400&q=80', desc: '34" ultrawide curved monitor. 21:9, 144Hz, FreeSync Premium.' },
  'Gaming Monitor 144Hz': { price: '$349', img: 'https://images.unsplash.com/photo-1598986646512-9330bcc4c0dc?w=400&q=80', desc: '27" gaming monitor. 144Hz, 1ms response, Full HD IPS.' },
  'Mini Projector 4K': { price: '$299', img: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80', desc: 'Portable 4K projector. 200" image, built-in speakers, WiFi.' },
};

let currentProduct = '';

function openProductModal(name) {
  const p = products[name];
  if (!p) return;

  currentProduct = name;
  document.getElementById('modalImg').src = p.img;
  document.getElementById('modalImg').alt = name;
  document.getElementById('modalName').textContent = name;
  document.getElementById('modalPrice').textContent = p.price;
  document.getElementById('modalDesc').textContent = p.desc;
  document.getElementById('modalAddCart').onclick = () => addToCart(name);

  loadModalComments(name);
  document.getElementById('productModal').classList.add('open');
}

function closeProductModal() {
  document.getElementById('productModal').classList.remove('open');
}

function loadModalComments(productName) {
  fetch(`php/comments.php?action=get&product=${encodeURIComponent(productName)}`)
    .then(r => r.json())
    .then(comments => {
      const list = document.getElementById('modalCommentsList');
      list.innerHTML = '';
      if (comments.length === 0) {
        list.innerHTML = '<p class="empty-msg">No reviews yet. Be the first!</p>';
        return;
      }
      comments.forEach(c => {
        const div = document.createElement('div');
        div.className = 'comment-card';

        const author = document.createElement('div');
        author.className = 'comment-author';
        author.textContent = c.author;

        const text = document.createElement('div');
        text.className = 'comment-text';
        text.textContent = c.text;

        const date = document.createElement('div');
        date.className = 'comment-date';
        date.textContent = new Date(c.created_at).toLocaleDateString();

        div.appendChild(author);
        div.appendChild(text);
        div.appendChild(date);
        list.appendChild(div);
      });
    });
}

function submitModalComment() {
  const author = document.getElementById('modalAuthor').value.trim();
  const text = document.getElementById('modalText').value.trim();

  if (!author || !text) {
    alert('Please fill in all fields!');
    return;
  }

  fetch('php/comments.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `author=${encodeURIComponent(author)}&text=${encodeURIComponent(text)}&product=${encodeURIComponent(currentProduct)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('modalAuthor').value = '';
      document.getElementById('modalText').value = '';
      loadModalComments(currentProduct);
    }
  });
}