/* ════════════════════════════════════════════
   popup.js — HackTheShop Attack Detector
   Include on every page with:
   <script src="popup.js"></script>
   ════════════════════════════════════════════ */

// Auto-check for detected attack on every page load
window.addEventListener('load', function () {
    fetch('detector.php')
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.detected) {
                showAttackPopup(res.data);
            }
        })
        .catch(function () {
            // Silent fail — popup is optional enhancement
        });
});

/* ── MAIN POPUP FUNCTION ── */
function showAttackPopup(attack) {
    // Don't stack popups
    if (document.getElementById('attack-overlay')) return;

    injectStyles();
    launchConfetti(attack.color);
    playClap();

    const overlay       = document.createElement('div');
    overlay.id          = 'attack-overlay';
    overlay.className   = 'hs-overlay';

    overlay.innerHTML = `
        <div class="hs-modal" style="border-color:${attack.color};box-shadow:0 0 50px ${attack.color}44;">

            <div class="hs-emoji">${attack.emoji}</div>

            <span class="hs-badge" style="background:${attack.color};">
                ${attack.severity}
            </span>

            <h2 class="hs-title" style="color:${attack.color};">
                🎉 Attack Successful: ${attack.name}
            </h2>

            <div class="hs-label hs-cyan">WHAT HAPPENED</div>
            <p class="hs-text">${attack.what}</p>

            <div class="hs-label hs-yellow">WHY IT IS DANGEROUS</div>
            <p class="hs-text">${attack.why}</p>

            <div class="hs-label hs-green">HOW TO FIX IT</div>
            <div class="hs-fix">${attack.fix}</div>

            <div class="hs-label hs-red">REAL-WORLD IMPACT</div>
            <p class="hs-text">${attack.impact}</p>

            <div class="hs-buttons">
                <button class="hs-btn-secondary" id="hs-close-btn">Close</button>
                <button class="hs-btn-primary" id="hs-got-btn"
                    style="background:${attack.color};">
                    Got it ✓
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Close handlers
    document.getElementById('hs-close-btn').addEventListener('click', closePopup);
    document.getElementById('hs-got-btn').addEventListener('click', closePopup);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closePopup();
    });

    // ESC key closes
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            closePopup();
            document.removeEventListener('keydown', escHandler);
        }
    });
}

function closePopup() {
    const el = document.getElementById('attack-overlay');
    if (el) el.remove();
}

/* ── CONFETTI ── */
function launchConfetti(primaryColor) {
    const colors = [primaryColor, '#7c3aed', '#22d3ee', '#a78bfa', '#f59e0b', '#22c55e', '#e94560'];

    for (let i = 0; i < 90; i++) {
        setTimeout(function () {
            const dot   = document.createElement('div');
            const size  = 6 + Math.random() * 7;
            const shape = Math.random() > 0.5 ? '50%' : '2px';

            dot.style.cssText = [
                'position:fixed',
                'pointer-events:none',
                'z-index:10000',
                'border-radius:' + shape,
                'left:'          + (Math.random() * 100) + 'vw',
                'top:-12px',
                'width:'         + size + 'px',
                'height:'        + size + 'px',
                'background:'    + colors[Math.floor(Math.random() * colors.length)],
                'animation:hsFall ' + (1.4 + Math.random() * 2) + 's linear forwards'
            ].join(';');

            document.body.appendChild(dot);
            setTimeout(function () { dot.remove(); }, 4000);
        }, i * 28);
    }
}

/* ── CLAP SOUND ── */
function playClap() {
    try {
        const audio = new Audio('assets/sounds/clap.mp3');
        audio.volume = 0.45;
        audio.play().catch(function () {});
    } catch (e) {
        // No audio file yet — silent fail
    }
}

/* ── INJECT CSS ONCE ── */
function injectStyles() {
    if (document.getElementById('hs-popup-style')) return;

    const s     = document.createElement('style');
    s.id        = 'hs-popup-style';
    s.innerHTML = `
        @keyframes hsFadeIn {
            from { opacity:0; }
            to   { opacity:1; }
        }
        @keyframes hsFall {
            0%   { transform:translateY(-10px) rotate(0deg);   opacity:1; }
            100% { transform:translateY(105vh)  rotate(720deg); opacity:0; }
        }
        @keyframes hsSlideUp {
            from { opacity:0; transform:translateY(30px); }
            to   { opacity:1; transform:translateY(0);    }
        }

        .hs-overlay {
            position:fixed; inset:0;
            background:rgba(0,0,0,0.88);
            z-index:9999;
            display:flex;
            align-items:center;
            justify-content:center;
            animation:hsFadeIn 0.3s ease;
            padding:16px;
        }

        .hs-modal {
            background:#13131f;
            border:2px solid #e94560;
            border-radius:16px;
            padding:30px;
            max-width:520px;
            width:100%;
            font-family:'Courier New',monospace;
            color:#e2d9f3;
            animation:hsSlideUp 0.35s ease;
            max-height:90vh;
            overflow-y:auto;
        }

        .hs-modal::-webkit-scrollbar { width:3px; }
        .hs-modal::-webkit-scrollbar-thumb { background:#3b2f6e; border-radius:3px; }

        .hs-emoji {
            font-size:44px;
            text-align:center;
            margin-bottom:10px;
        }

        .hs-badge {
            display:inline-block;
            color:white;
            font-size:10px;
            letter-spacing:2px;
            padding:3px 12px;
            border-radius:20px;
            margin-bottom:14px;
        }

        .hs-title {
            font-size:16px;
            margin-bottom:16px;
            line-height:1.4;
        }

        .hs-label {
            font-size:10px;
            letter-spacing:1.5px;
            margin-top:14px;
            margin-bottom:4px;
            font-weight:bold;
        }

        .hs-cyan   { color:#22d3ee; }
        .hs-yellow { color:#f59e0b; }
        .hs-green  { color:#22c55e; }
        .hs-red    { color:#e94560; }

        .hs-text {
            font-size:13px;
            line-height:1.65;
            color:#cbd5e1;
            margin:0;
        }

        .hs-fix {
            background:#0d0d14;
            border-left:3px solid #22c55e;
            padding:10px 14px;
            border-radius:6px;
            font-size:12px;
            color:#86efac;
            line-height:1.7;
            margin-top:4px;
            word-break:break-word;
        }

        .hs-buttons {
            display:flex;
            gap:10px;
            justify-content:flex-end;
            margin-top:22px;
        }

        .hs-btn-secondary {
            background:transparent;
            border:1px solid #7c3aed;
            color:#a78bfa;
            padding:10px 20px;
            border-radius:8px;
            cursor:pointer;
            font-family:'Courier New',monospace;
            font-size:13px;
            transition:background 0.2s;
        }
        .hs-btn-secondary:hover { background:#1e0a3c; }

        .hs-btn-primary {
            border:none;
            color:white;
            padding:10px 20px;
            border-radius:8px;
            cursor:pointer;
            font-family:'Courier New',monospace;
            font-size:13px;
            font-weight:bold;
            transition:opacity 0.2s;
        }
        .hs-btn-primary:hover { opacity:0.85; }
    `;
    document.head.appendChild(s);
}