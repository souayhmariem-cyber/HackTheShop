/* ═══════════════════════════════════════════════════════════════
   REWARD SYSTEM — Challenge Unlock + Spinning Reward Animation
   ═══════════════════════════════════════════════════════════════ */

let completedCategories = [];
let userRewards         = [];

// Load progress on page load
async function loadProgress() {
  try {
    const res  = await fetch('php/reward.php?action=get_progress');
    const data = await res.json();
    completedCategories = data.completed || [];
    userRewards         = data.rewards   || [];
    updateUnlockButtons();
    renderRewardBadges();
  } catch (e) {
    // Not logged in or DB unavailable — silent fail
  }
}

function updateUnlockButtons() {
  document.querySelectorAll('.unlock-btn').forEach(btn => {
    const catId = parseInt(btn.dataset.categoryId);
    if (completedCategories.includes(catId)) {
      btn.innerHTML = '<i class="fa fa-check"></i> Unlocked!';
      btn.classList.add('unlocked');
      btn.disabled = true;
    }
  });
}

function renderRewardBadges() {
  const container = document.getElementById('rewardsContainer');
  if (!container) return;
  if (userRewards.length === 0) {
    container.innerHTML = '<p class="empty-msg" style="text-align:center;padding:20px;color:#534AB7;">Complete challenges to win rewards! 🎁</p>';
    return;
  }
  container.innerHTML = userRewards.map(r => `
    <div class="reward-card">
      <img src="${r.img}" alt="${r.name}" class="reward-img">
      <div class="reward-name">${r.name}</div>
      <div class="reward-price" style="color:#a78bfa;">$${parseFloat(r.price).toFixed(2)}</div>
      <div class="reward-cat" style="color:#22d3ee;font-size:11px;">${r.category_name}</div>
    </div>
  `).join('');
}

// ── Unlock Flow ─────────────────────────────────────────────────────────────
function startChallenge(categoryId, challengeUrl, categoryName) {
  // Store intent in sessionStorage so we can claim on return
  sessionStorage.setItem('pendingClaim_categoryId',   categoryId);
  sessionStorage.setItem('pendingClaim_categoryName', categoryName);
  sessionStorage.setItem('pendingClaim_time',         Date.now().toString());

  // Open challenge in new tab
  window.open(challengeUrl, '_blank');

  // Show "I completed it" banner after 3 sec
  setTimeout(() => {
    showClaimBanner(categoryId, categoryName);
  }, 3000);
}

function showClaimBanner(categoryId, categoryName) {
  let banner = document.getElementById('claimBanner');
  if (!banner) {
    banner = document.createElement('div');
    banner.id = 'claimBanner';
    banner.style.cssText = `
      position:fixed;bottom:0;left:0;right:0;
      background:linear-gradient(135deg,rgba(30,10,60,0.98),rgba(19,19,31,0.98));
      border-top:2px solid #7c3aed;
      padding:20px 24px;
      display:flex;align-items:center;justify-content:space-between;
      z-index:9998;gap:16px;flex-wrap:wrap;
      font-family:'Courier New',monospace;
      box-shadow:0 -10px 40px rgba(124,58,237,0.3);
      animation:slideUp 0.4s ease;
    `;
    document.body.appendChild(banner);
  }

  banner.innerHTML = `
    <div>
      <div style="color:#a78bfa;font-weight:bold;margin-bottom:4px;">🏆 Challenge: <em>${categoryName}</em></div>
      <div style="color:#e2d9f3;font-size:13px;">Did you complete the challenge? Click below to claim your reward!</div>
    </div>
    <div style="display:flex;gap:10px;flex-shrink:0;">
      <button onclick="claimReward(${categoryId},'${categoryName}')" style="
        background:#7c3aed;border:none;color:white;
        padding:10px 22px;border-radius:25px;cursor:pointer;
        font-family:inherit;font-weight:bold;font-size:0.9rem;
        transition:background 0.2s;
      " onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
        🎁 Claim Reward!
      </button>
      <button onclick="dismissClaim()" style="
        background:transparent;border:1px solid #534AB7;color:#a78bfa;
        padding:10px 16px;border-radius:25px;cursor:pointer;font-family:inherit;
      ">✕</button>
    </div>
  `;
  banner.style.display = 'flex';
}

function dismissClaim() {
  const b = document.getElementById('claimBanner');
  if (b) b.style.display = 'none';
}

async function claimReward(categoryId, categoryName) {
  dismissClaim();

  // Show spinning overlay
  showSpinner(categoryName);

  try {
    const res  = await fetch('php/reward.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    `action=claim&category_id=${categoryId}`
    });
    const data = await res.json();

    setTimeout(() => {
      hideSpinner();
      if (data.success) {
        completedCategories.push(categoryId);
        updateUnlockButtons();
        showRewardModal(data.product, data.badge);
        loadProgress();
      } else if (data.already_done) {
        showToast('⚠️ You already completed this challenge!');
      } else if (data.error === 'Not logged in') {
        showToast('🔐 Please login to claim rewards!');
        setTimeout(() => { window.location.href = 'login.html'; }, 1500);
      } else {
        showToast('❌ ' + (data.error || 'Something went wrong.'));
      }
    }, 2800);

  } catch (e) {
    hideSpinner();
    showToast('❌ Connection error. Please try again.');
  }
}

// ── Spinning Reward Animation ────────────────────────────────────────────────
function showSpinner(categoryName) {
  let overlay = document.getElementById('spinnerOverlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'spinnerOverlay';
    overlay.style.cssText = `
      position:fixed;inset:0;
      background:rgba(8,8,16,0.95);
      display:flex;flex-direction:column;
      align-items:center;justify-content:center;
      z-index:10000;
      font-family:'Courier New',monospace;
    `;
    document.body.appendChild(overlay);
  }

  overlay.innerHTML = `
    <style>
      @keyframes spin360 { to { transform: rotate(360deg); } }
      @keyframes pulseBig { 0%,100% { transform:scale(1); } 50% { transform:scale(1.12); } }
      .spinner-ring {
        width:120px;height:120px;border-radius:50%;
        border:4px solid #1e1a2e;
        border-top-color:#7c3aed;
        border-right-color:#a78bfa;
        animation:spin360 0.8s linear infinite;
        margin-bottom:24px;
      }
      .spinner-inner {
        width:80px;height:80px;border-radius:50%;
        border:3px solid #1e1a2e;
        border-top-color:#22d3ee;
        animation:spin360 0.6s linear infinite reverse;
        position:absolute;
        top:50%;left:50%;transform:translate(-50%,-50%);
      }
      .spinner-wrap { position:relative;width:120px;height:120px;margin-bottom:24px; }
    </style>
    <div class="spinner-wrap">
      <div class="spinner-ring"></div>
      <div class="spinner-inner"></div>
    </div>
    <div style="color:#a78bfa;font-size:1.1rem;font-weight:bold;letter-spacing:2px;animation:pulseBig 1s ease-in-out infinite;">
      🎰 DRAWING YOUR REWARD...
    </div>
    <div style="color:#534AB7;font-size:0.8rem;margin-top:12px;">Category: ${categoryName}</div>
  `;
  overlay.style.display = 'flex';
}

function hideSpinner() {
  const o = document.getElementById('spinnerOverlay');
  if (o) o.style.display = 'none';
}

// ── Reward Won Modal ─────────────────────────────────────────────────────────
function showRewardModal(product, badgeName) {
  let modal = document.getElementById('rewardWonModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'rewardWonModal';
    modal.style.cssText = `
      position:fixed;inset:0;
      background:rgba(8,8,16,0.9);
      display:flex;align-items:center;justify-content:center;
      z-index:10001;padding:20px;
    `;
    document.body.appendChild(modal);
  }

  modal.innerHTML = `
    <style>
      @keyframes popIn { from { transform:scale(0.5);opacity:0; } to { transform:scale(1);opacity:1; } }
      @keyframes confettiFall {
        0%   { transform:translateY(-20px) rotate(0deg);   opacity:1; }
        100% { transform:translateY(200px) rotate(720deg); opacity:0; }
      }
      .confetti-dot {
        position:absolute;width:8px;height:8px;border-radius:50%;
        animation:confettiFall 1.5s ease-in forwards;
      }
    </style>
    <div style="
      background:linear-gradient(135deg,#0d0d14,#13131f);
      border:2px solid #7c3aed;border-radius:20px;
      max-width:380px;width:100%;text-align:center;
      padding:32px 24px;font-family:'Courier New',monospace;
      animation:popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275);
      position:relative;overflow:hidden;
      box-shadow:0 0 60px rgba(124,58,237,0.4);
    " id="rewardCard">
      <div id="confettiContainer" style="position:absolute;top:0;left:0;right:0;height:0;pointer-events:none;"></div>
      <div style="font-size:2.5rem;margin-bottom:8px;">🎉</div>
      <h2 style="color:#a78bfa;margin-bottom:4px;font-size:1.3rem;">YOU WON!</h2>
      <p style="color:#534AB7;font-size:0.8rem;margin-bottom:20px;">Challenge completed successfully</p>
      <img src="${product.img}" alt="${product.name}" style="
        width:100%;max-width:200px;height:140px;object-fit:cover;
        border-radius:12px;border:2px solid #7c3aed;margin-bottom:16px;
      ">
      <div style="color:#e2d9f3;font-size:1.1rem;font-weight:bold;margin-bottom:6px;">${product.name}</div>
      <div style="color:#a78bfa;font-size:1.3rem;font-weight:bold;margin-bottom:16px;">$${parseFloat(product.price).toFixed(2)}</div>
      ${badgeName ? `<div style="
        display:inline-block;background:rgba(124,58,237,0.2);border:1px solid #7c3aed;
        color:#a78bfa;padding:6px 16px;border-radius:20px;font-size:0.8rem;margin-bottom:20px;
      ">🏆 Badge: ${badgeName}</div>` : ''}
      <br>
      <button onclick="closeRewardModal()" style="
        background:#7c3aed;border:none;color:white;
        padding:12px 32px;border-radius:25px;cursor:pointer;
        font-family:inherit;font-size:0.9rem;font-weight:bold;
        transition:background 0.2s;
      " onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
        Awesome! 🚀
      </button>
    </div>
  `;

  modal.style.display = 'flex';

  // Confetti burst
  const cc = document.getElementById('confettiContainer');
  const colors = ['#7c3aed','#a78bfa','#22d3ee','#f472b6','#fbbf24','#34d399'];
  for (let i = 0; i < 30; i++) {
    const dot = document.createElement('div');
    dot.className = 'confetti-dot';
    dot.style.cssText = `
      left:${Math.random()*100}%;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      animation-delay:${Math.random()*0.8}s;
      animation-duration:${1.2 + Math.random()*0.8}s;
    `;
    cc.appendChild(dot);
  }
}

function closeRewardModal() {
  const m = document.getElementById('rewardWonModal');
  if (m) m.style.display = 'none';
}

// ── Check for pending claim on page load ────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
  loadProgress();

  const pendingId   = sessionStorage.getItem('pendingClaim_categoryId');
  const pendingName = sessionStorage.getItem('pendingClaim_categoryName');
  const pendingTime = parseInt(sessionStorage.getItem('pendingClaim_time') || '0');

  // Show banner if returned within 2 hours
  if (pendingId && (Date.now() - pendingTime) < 7200000) {
    const catId = parseInt(pendingId);
    if (!completedCategories.includes(catId)) {
      setTimeout(() => showClaimBanner(catId, pendingName), 800);
    }
  }
});

// CSS animation for banner
const style = document.createElement('style');
style.textContent = `@keyframes slideUp { from { transform:translateY(100%); } to { transform:translateY(0); } }`;
document.head.appendChild(style);