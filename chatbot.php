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
#particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}
.chat-header {
    background: rgba(19, 19, 31, 0.95);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #7c3aed;
    position: relative;
    z-index: 2;
}
.avatar-wrap {
    width: 58px;
    height: 64px;
    flex-shrink: 0;
}
.avatar-img {
    width: 58px;
    height: 64px;
    background: transparent;
    border: none;
    border-radius: 0;
    overflow: visible;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    animation: wave-bob 3s ease-in-out infinite;
    transform-origin: center bottom;
}
.avatar-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}
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
.chat-messages {
    flex: 1;
    padding: 14px;
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
.bot-row {
    display: flex;
    align-items: flex-end;
    gap: 7px;
    align-self: flex-start;
    max-width: 92%;
    animation: msg-in 0.25s ease;
}
.bubble-sticker {
    width: 34px;
    height: 38px;
    flex-shrink: 0;
    object-fit: contain;
    display: block;
    animation: wave-bob 3s ease-in-out infinite;
    transform-origin: center bottom;
}
.message {
    padding: 11px 14px;
    border-radius: 14px;
    font-size: 0.88rem;
    line-height: 1.55;
    word-wrap: break-word;
}
.bot {
    background: rgba(19, 19, 31, 0.92);
    color: #e2d9f3;
    border: 1px solid #3b2f6e;
    border-radius: 0 14px 14px 14px;
}
.user {
    background: rgba(30, 10, 60, 0.95);
    color: #d8b4fe;
    border: 1px solid #7c3aed;
    border-radius: 14px 14px 0 14px;
    align-self: flex-end;
    max-width: 85%;
    animation: msg-in 0.25s ease;
}
.typing {
    font-style: italic;
    color: #534AB7;
}
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
@keyframes wave-bob {
    0%   { transform: translateY(0px)   rotate(-2deg); }
    25%  { transform: translateY(-5px)  rotate(2deg);  }
    50%  { transform: translateY(-3px)  rotate(-1deg); }
    75%  { transform: translateY(-6px)  rotate(3deg);  }
    100% { transform: translateY(0px)   rotate(-2deg); }
}
@keyframes wave-excited {
    0%   { transform: translateY(0px)   rotate(0deg);  }
    15%  { transform: translateY(-8px)  rotate(8deg);  }
    30%  { transform: translateY(-4px)  rotate(-6deg); }
    45%  { transform: translateY(-10px) rotate(10deg); }
    60%  { transform: translateY(-5px)  rotate(-4deg); }
    75%  { transform: translateY(-8px)  rotate(7deg);  }
    100% { transform: translateY(0px)   rotate(0deg);  }
}
@keyframes blink {
    0%, 100% { opacity: 1;   }
    50%       { opacity: 0.2; }
}
@keyframes msg-in {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0);   }
}
@media (max-width: 500px) {
    body { padding: 0; }
    .chat-container { height: 100vh; max-width: 100%; border-radius: 0; }
}
</style>
</head>
<body>

<div class="chat-container">
    <canvas id="particles"></canvas>

    <div class="chat-header">
        <div class="avatar-wrap">
            <div class="avatar-img" id="echoAvatar">
                <img src="images/echo.png" alt="Echo">
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

    <div class="chat-messages" id="messages"></div>

    <div class="quick">
        <button onclick="quickMsg('Where is my order?')">Order</button>
        <button onclick="quickMsg('Refund policy')">Refund</button>
        <button onclick="quickMsg('Shipping time')">Shipping</button>
    </div>

    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type a message...">
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
const canvas = document.getElementById('particles');
const ctx    = canvas.getContext('2d');
const cont   = canvas.parentElement;

function resizeCanvas() {
    canvas.width  = cont.offsetWidth;
    canvas.height = cont.offsetHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

const particles = Array.from({ length: 70 }, () => ({
    x:          Math.random() * canvas.width,
    y:          Math.random() * canvas.height,
    r:          Math.random() * 1.6 + 0.3,
    vx:         (Math.random() - 0.5) * 0.35,
    vy:         (Math.random() - 0.5) * 0.35,
    alpha:      Math.random(),
    alphaDir:   Math.random() > 0.5 ? 1 : -1,
    alphaSpeed: Math.random() * 0.008 + 0.002,
    color:      Math.random() > 0.85
                    ? (Math.random() > 0.5 ? '124,58,237' : '34,211,238')
                    : '255,255,255'
}));

function drawParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
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
    particles.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;
        if (p.x < 0)             p.x = canvas.width;
        if (p.x > canvas.width)  p.x = 0;
        if (p.y < 0)             p.y = canvas.height;
        if (p.y > canvas.height) p.y = 0;
        p.alpha += p.alphaDir * p.alphaSpeed;
        if (p.alpha >= 1)    { p.alpha = 1;    p.alphaDir = -1; }
        if (p.alpha <= 0.04) { p.alpha = 0.04; p.alphaDir =  1; }
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${p.color},${p.alpha})`;
        ctx.fill();
    });
    requestAnimationFrame(drawParticles);
}
drawParticles();

const avatar = document.getElementById('echoAvatar');

function triggerExcitedWave() {
    avatar.style.animation = 'none';
    void avatar.offsetHeight;
    avatar.style.animation = 'wave-excited 0.6s ease-in-out 2';
    setTimeout(() => {
        avatar.style.animation = 'wave-bob 3s ease-in-out infinite';
    }, 1300);
}

const messagesEl = document.getElementById('messages');
const input      = document.getElementById('userInput');

function addBotMessage(html) {
    const row = document.createElement('div');
    row.className = 'bot-row';
    const sticker     = document.createElement('img');
    sticker.src       = 'images/echo.png';
    sticker.alt       = 'Echo';
    sticker.className = 'bubble-sticker';
    sticker.style.animationDelay = (Math.random() * 1.5).toFixed(2) + 's';
    const bubble      = document.createElement('div');
    bubble.className  = 'message bot';
    bubble.innerHTML  = html;
    row.appendChild(sticker);
    row.appendChild(bubble);
    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return row;
}

function addUserMessage(text) {
    const div       = document.createElement('div');
    div.className   = 'message user';
    div.textContent = text;
    messagesEl.appendChild(div);
    messagesEl.scrollTop = messagesEl.scrollHeight;
}

function quickMsg(text) {
    input.value = text;
    sendMessage();
}

function sendMessage() {
    const msg = input.value.trim();
    if (msg === '') return;
    addUserMessage(msg);
    input.value = '';
    triggerExcitedWave();
    const typingRow = addBotMessage('<span class="typing">...</span>');
    fetch('reply.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    'message=' + encodeURIComponent(msg)
    })
    .then(r => r.json())
    .then(data => {
        typingRow.remove();
        addBotMessage(data.reply);
        triggerExcitedWave();
    })
    .catch(() => {
        typingRow.remove();
        addBotMessage('&#9888; Connection error. Please try again.');
    });
}

input.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});

window.addEventListener('load', function() {
    addBotMessage(
        'Hello 👋 Welcome to <b style="color:#a78bfa;">HackTheShop</b>.<br><br>' +
        '<span style="color:#22d3ee;">&#9656;</span> Orders / Refunds / Products<br><br>' +
        'Ask me anything.'
    );
});
</script>

</body>
</html>