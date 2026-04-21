<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Echo.exe</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Courier New', monospace;
    background: #080810;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* ── CONTAINER ── */
.chat-container {
    width: 100%;
    max-width: 430px;
    height: 700px;
    background: #0d0d14;
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid #7c3aed;
    box-shadow: 0 0 60px rgba(124, 58, 237, 0.2);
    display: flex;
    flex-direction: column;
    position: relative;
}

/* ── PARTICLE CANVAS (background layer) ── */
#particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

/* ── HEADER ── */
.chat-header {
    background: rgba(19, 19, 31, 0.95);
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #7c3aed;
    position: relative;
    z-index: 2;
}

/* ── AVATAR ── */
.avatar-wrap {
    position: relative;
    width: 54px;
    height: 54px;
    flex-shrink: 0;
}

.avatar-ring {
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    border: 2px solid #7c3aed;
    animation: pulse-ring 2s ease-in-out infinite;
}

.avatar-img {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: #1a1a2e;
    border: 2px solid #3b2f6e;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.6rem;
    animation: wave-bob 3s ease-in-out infinite;
    transform-origin: center bottom;
}

/*
    ════════════════════════════════════════
    TO USE YOUR CHARACTER PHOTO:
    Replace the emoji inside .avatar-img with:
    <img src="images/your-character.png"
         style="width:100%;height:100%;object-fit:cover;">
    Use a PNG with transparent background for best result.
    ════════════════════════════════════════
*/

.bot-name {
    color: #a78bfa;
    font-size: 1rem;
    font-weight: bold;
    letter-spacing: 1px;
}

.bot-status {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22d3ee;
    animation: blink 1.5s ease-in-out infinite;
}

.status-text {
    color: #22d3ee;
    font-size: 11px;
    letter-spacing: 0.5px;
}

.header-tag {
    margin-left: auto;
    font-size: 10px;
    color: #534AB7;
    letter-spacing: 2px;
}

/* ── MESSAGES AREA ── */
.chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    z-index: 2;
    background: transparent;
}

.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: #3b2f6e; border-radius: 3px; }

/* ── BUBBLES ── */
.message {
    max-width: 85%;
    padding: 12px 15px;
    border-radius: 14px;
    font-size: 0.88rem;
    line-height: 1.55;
    word-wrap: break-word;
    animation: msg-in 0.25s ease;
}

.bot {
    background: rgba(19, 19, 31, 0.92);
    color: #e2d9f3;
    border: 1px solid #3b2f6e;
    border-radius: 0 14px 14px 14px;
    align-self: flex-start;
}

.user {
    background: rgba(30, 10, 60, 0.95);
    color: #d8b4fe;
    border: 1px solid #7c3aed;
    border-radius: 14px 14px 0 14px;
    align-self: flex-end;
}

.typing {
    font-style: italic;
    color: #534AB7;
}

/* ── QUICK BUTTONS ── */
.quick {
    padding: 10px 14px;
    background: rgba(13, 13, 20, 0.95);
    border-top: 1px solid #1e1a2e;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    position: relative;
    z-index: 2;
}

.quick button {
    padding: 6px 14px;
    border: 1px solid #7c3aed;
    border-radius: 20px;
    background: transparent;
    color: #a78bfa;
    cursor: pointer;
    font-size: 0.78rem;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.5px;
    transition: background 0.2s, color 0.2s;
}

.quick button:hover {
    background: #1e0a3c;
    color: #d8b4fe;
}

/* ── INPUT ── */
.chat-input {
    display: flex;
    gap: 8px;
    padding: 12px 14px;
    background: rgba(13, 13, 20, 0.95);
    border-top: 1px solid #1e1a2e;
    position: relative;
    z-index: 2;
}

.chat-input input {
    flex: 1;
    background: #13131f;
    border: 1px solid #3b2f6e;
    border-radius: 25px;
    padding: 11px 16px;
    color: #e2d9f3;
    font-size: 0.88rem;
    font-family: 'Courier New', monospace;
    outline: none;
    transition: border-color 0.2s;
}

.chat-input input::placeholder { color: #534AB7; }
.chat-input input:focus { border-color: #7c3aed; }

.chat-input button {
    background: #7c3aed;
    border: none;
    color: white;
    padding: 0 20px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    letter-spacing: 1px;
    transition: background 0.2s;
}

.chat-input button:hover { background: #6d28d9; }

/* ── ANIMATIONS ── */

/* Avatar gentle float + tilt */
@keyframes wave-bob {
    0%   { transform: translateY(0px)   rotate(-2deg); }
    25%  { transform: translateY(-5px)  rotate(2deg);  }
    50%  { transform: translateY(-3px)  rotate(-1deg); }
    75%  { transform: translateY(-6px)  rotate(3deg);  }
    100% { transform: translateY(0px)   rotate(-2deg); }
}

/* Quick excited wave on message send/receive */
@keyframes wave-excited {
    0%   { transform: translateY(0px)    rotate(0deg);  }
    15%  { transform: translateY(-8px)   rotate(8deg);  }
    30%  { transform: translateY(-4px)   rotate(-6deg); }
    45%  { transform: translateY(-10px)  rotate(10deg); }
    60%  { transform: translateY(-5px)   rotate(-4deg); }
    75%  { transform: translateY(-8px)   rotate(7deg);  }
    100% { transform: translateY(0px)    rotate(0deg);  }
}

/* Glowing ring pulse */
@keyframes pulse-ring {
    0%, 100% { opacity: 0.5; transform: scale(1);    }
    50%       { opacity: 1;   transform: scale(1.1); }
}

/* Status dot blink */
@keyframes blink {
    0%, 100% { opacity: 1;   }
    50%       { opacity: 0.2; }
}

/* Message slide-in */
@keyframes msg-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0);   }
}

/* Mobile */
@media (max-width: 500px) {
    body { padding: 0; }
    .chat-container { height: 100vh; max-width: 100%; border-radius: 0; }
}
</style>
</head>
<body>

<div class="chat-container">

    <!-- PARTICLE CANVAS -->
    <canvas id="particles"></canvas>

    <!-- HEADER -->
    <div class="chat-header">

        <div class="avatar-wrap">
            <div class="avatar-ring"></div>
            <div class="avatar-img" id="echoAvatar">
               
                <img src="echo.png" style="width:100%;height:100%;object-fit:cover;">
            </div>
        </div>

        <div>
            <div class="bot-name">Echo.exe</div>
            <div class="bot-status">
                <span class="status-dot"></span>
                <span class="status-text">ONLINE</span>
            </div>
        </div>

        <div class="header-tag">HackTheShop</div>
    </div>

    <!-- MESSAGES -->
    <div class="chat-messages" id="messages">
        <div class="message bot">
            Hello 👋 Welcome to <b style="color:#a78bfa;">HackTheShop</b>.<br><br>
            I can help you with:<br>
            <span style="color:#22d3ee;">&#9656;</span> Orders / Refunds / Products<br><br>
            Ask me anything.
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick">
        <button onclick="quickMsg('Where is my order?')">Order</button>
        <button onclick="quickMsg('Refund policy')">Refund</button>
        <button onclick="quickMsg('Shipping time')">Shipping</button>
    </div>

    <!-- INPUT -->
    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type a message...">
        <button onclick="sendMessage()">Send</button>
    </div>

</div>

<script>
/* ════════════════════════════════════
   PARTICLE BACKGROUND ANIMATION
   ════════════════════════════════════ */
const canvas = document.getElementById('particles');
const ctx    = canvas.getContext('2d');
const cont   = canvas.parentElement;

function resizeCanvas() {
    canvas.width  = cont.offsetWidth;
    canvas.height = cont.offsetHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

// Generate particles
const PARTICLE_COUNT = 70;
const particles = Array.from({ length: PARTICLE_COUNT }, () => ({
    x:          Math.random() * canvas.width,
    y:          Math.random() * canvas.height,
    r:          Math.random() * 1.6 + 0.3,
    vx:         (Math.random() - 0.5) * 0.35,
    vy:         (Math.random() - 0.5) * 0.35,
    alpha:      Math.random(),
    alphaDir:   Math.random() > 0.5 ? 1 : -1,
    alphaSpeed: Math.random() * 0.008 + 0.002,
    // occasional cyan/purple tint
    color:      Math.random() > 0.85
                    ? (Math.random() > 0.5 ? '124,58,237' : '34,211,238')
                    : '255,255,255'
}));

function drawParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw connection lines between close particles
    for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
            const dx   = particles[i].x - particles[j].x;
            const dy   = particles[i].y - particles[j].y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 70) {
                ctx.beginPath();
                ctx.moveTo(particles[i].x, particles[i].y);
                ctx.lineTo(particles[j].x, particles[j].y);
                ctx.strokeStyle = `rgba(124,58,237,${0.12 * (1 - dist / 70)})`;
                ctx.lineWidth   = 0.4;
                ctx.stroke();
            }
        }
    }

    // Draw and move each particle
    particles.forEach(p => {
        // Move
        p.x += p.vx;
        p.y += p.vy;

        // Wrap around edges
        if (p.x < 0)             p.x = canvas.width;
        if (p.x > canvas.width)  p.x = 0;
        if (p.y < 0)             p.y = canvas.height;
        if (p.y > canvas.height) p.y = 0;

        // Pulse alpha
        p.alpha += p.alphaDir * p.alphaSpeed;
        if (p.alpha >= 1)    { p.alpha = 1;    p.alphaDir = -1; }
        if (p.alpha <= 0.04) { p.alpha = 0.04; p.alphaDir =  1; }

        // Draw dot
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${p.color},${p.alpha})`;
        ctx.fill();
    });

    requestAnimationFrame(drawParticles);
}
drawParticles();


/* ════════════════════════════════════
   AVATAR WAVE ANIMATION
   ════════════════════════════════════ */
const avatar = document.getElementById('echoAvatar');

function triggerExcitedWave() {
    // Switch to excited wave
    avatar.style.animation = 'none';
    // Force reflow so the reset registers
    void avatar.offsetHeight;
    avatar.style.animation = 'wave-excited 0.6s ease-in-out 2';

    // After excited wave, return to gentle bob
    setTimeout(() => {
        avatar.style.animation = 'wave-bob 3s ease-in-out infinite';
    }, 1300);
}


/* ════════════════════════════════════
   CHAT LOGIC
   ════════════════════════════════════ */
const messages = document.getElementById('messages');
const input    = document.getElementById('userInput');

function addMessage(text, type) {
    const div       = document.createElement('div');
    div.className   = 'message ' + type;
    if (type === 'user') {
        div.textContent = text;
    } else {
        div.innerHTML = text;
    }
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
}

function quickMsg(text) {
    input.value = text;
    sendMessage();
}

function sendMessage() {
    const msg = input.value.trim();
    if (msg === '') return;

    addMessage(msg, 'user');
    input.value = '';
    triggerExcitedWave();

    const typing = addMessage('...', 'bot');
    typing.classList.add('typing');

    fetch('reply.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'message=' + encodeURIComponent(msg)
    })
    .then(r => r.json())
    .then(data => {
        typing.remove();
        addMessage(data.reply, 'bot');
        triggerExcitedWave();
    })
    .catch(() => {
        typing.remove();
        addMessage('&#9888; Connection error. Please try again.', 'bot');
    });
}

input.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});
</script>

</body>
</html>