<?php
session_start();
require 'db_connect.php';

$error        = null;
$loginSuccess = false;
$attackDetected = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // INTENTIONALLY VULNERABLE — Educational SQLi demo
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

    try {
        $result = $pdo->query($query);
        $user   = $result->fetch();

        if ($user) {
            $_SESSION['user']    = $user['nom'];
            $_SESSION['user_id'] = $user['id'];
            $loginSuccess = true;

            // Detect SQL Injection payload in the email field
            if (
                strpos($email, "'")  !== false ||
                strpos($email, '--') !== false ||
                stripos($email, ' or ')  !== false ||
                stripos($email, ' OR ')  !== false
            ) {
                $attackDetected = true;
            }

        } else {
            $error = "Invalid email or password!";
        }

    } catch (PDOException $e) {
        // Show DB error — also educational (reveals table structure hints)
        $error = "DB Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HackTheShop - Login Result</title>
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ── ATTACK POPUP OVERLAY ── */
    #attack-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.88);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }
    #attack-overlay.active { display: flex; }

    .attack-modal {
        background: #13131f;
        border: 2px solid #e94560;
        border-radius: 16px;
        padding: 32px;
        max-width: 520px;
        width: 90%;
        font-family: 'Courier New', monospace;
        color: #e2d9f3;
        box-shadow: 0 0 50px rgba(233,69,96,0.35);
        position: relative;
        z-index: 10;
    }

    .attack-modal h2   { color: #e94560; margin-bottom: 18px; font-size: 17px; }
    .attack-modal .section-label {
        font-size: 10px; letter-spacing: 1.5px; margin-bottom: 4px; margin-top: 14px;
    }
    .what-label  { color: #22d3ee; }
    .why-label   { color: #f59e0b; }
    .fix-label   { color: #22c55e; }
    .impact-label{ color: #e94560; }

    .attack-modal p    { font-size: 13px; line-height: 1.65; color: #cbd5e1; }
    .fix-box {
        background: #0d0d14; border-left: 3px solid #22c55e;
        padding: 10px 12px; border-radius: 6px; margin-top: 4px;
    }
    .fix-box code { font-size: 12px; color: #86efac; }

    .badge {
        display: inline-block;
        background: #e94560; color: white;
        font-size: 10px; letter-spacing: 2px;
        padding: 3px 10px; border-radius: 20px;
        margin-bottom: 14px;
    }

    .modal-buttons { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
    .btn-primary {
        background: #e94560; border: none; color: white;
        padding: 10px 22px; border-radius: 8px; cursor: pointer;
        font-family: 'Courier New', monospace; font-size: 13px;
    }
    .btn-secondary {
        background: transparent; border: 1px solid #7c3aed; color: #a78bfa;
        padding: 10px 22px; border-radius: 8px; cursor: pointer;
        font-family: 'Courier New', monospace; font-size: 13px;
    }

    /* Confetti particle */
    .confetti-dot {
        position: fixed; width: 8px; height: 8px; border-radius: 50%;
        pointer-events: none; z-index: 10000;
        animation: fall linear forwards;
    }
    @keyframes fall {
        0%   { transform: translateY(-10px) rotate(0deg);   opacity: 1; }
        100% { transform: translateY(105vh) rotate(720deg); opacity: 0; }
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  </style>
</head>
<body>

  <canvas id="particles"></canvas>

  <div class="wrapper">

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-left">
        <button class="burger" onclick="toggleSidebar()">
          <i class="fa fa-bars"></i>
        </button>
      </div>
      <div class="nav-center">
        <a href="../index.html" style="text-decoration:none;">
          <span class="logo">HackTheShop</span>
        </a>
      </div>
      <div class="nav-right">
        <button class="nav-icon"><i class="fa fa-shopping-cart"></i></button>
      </div>
    </nav>

    <!-- RESULT SECTION -->
    <section class="login-section">
      <div class="login-box">

        <?php if ($loginSuccess): ?>
          <div class="success-msg">
            ✅ Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>!
          </div>
          <a href="../index.html" class="login-btn"
             style="display:block;text-align:center;text-decoration:none;margin-top:16px;">
            Back to Shop
          </a>

        <?php elseif ($error): ?>
          <div class="error-msg">❌ <?php echo htmlspecialchars($error); ?></div>
          <a href="../login.html" class="login-btn"
             style="display:block;text-align:center;text-decoration:none;margin-top:16px;">
            Try Again
          </a>

        <?php else: ?>
          <a href="../login.html" class="login-btn"
             style="display:block;text-align:center;text-decoration:none;margin-top:16px;">
            Go to Login
          </a>
        <?php endif; ?>

      </div>
    </section>

    <footer class="footer">
      © 2025 HackTheShop — Educational Cybersecurity Project
    </footer>
  </div>

  <!-- ══════════════════════════════════
       ATTACK POPUP — only shown when
       SQLi payload was detected
       ══════════════════════════════════ -->
  <div id="attack-overlay" <?php if($attackDetected) echo 'class="active"'; ?>>
    <div class="attack-modal">

      <div style="font-size:46px;text-align:center;margin-bottom:10px;">💉</div>
      <div class="badge">CRITICAL</div>
      <h2>🎉 Attack Successful: SQL Injection — Auth Bypass</h2>

      <div class="section-label what-label">WHAT HAPPENED</div>
      <p>
        You injected SQL into the email field. The query became:<br><br>
        <code style="color:#f87171;font-size:12px;">
          SELECT * FROM users WHERE email = '<strong>' OR '1'='1'--</strong>' AND password = '...'
        </code><br><br>
        The condition <code style="color:#f87171;">OR '1'='1'</code> is always true,
        so the database returned the first user — without needing a real password.
      </p>

      <div class="section-label why-label">WHY IT'S DANGEROUS</div>
      <p>
        Any attacker can log in as any user — including admin — without knowing a single password.
        One payload can compromise every account in the database.
      </p>

      <div class="section-label fix-label">HOW TO FIX IT</div>
      <div class="fix-box">
        <code>
          $stmt = $pdo->prepare(<br>
          &nbsp;&nbsp;"SELECT * FROM users WHERE email = ? AND password = ?"<br>
          );<br>
          $stmt->execute([$email, $password]);
        </code>
      </div>

      <div class="section-label impact-label">REAL-WORLD IMPACT</div>
      <p>Full account takeover · Admin access · Complete data breach · OWASP Top 10 #3</p>

      <div class="modal-buttons">
        <button class="btn-secondary" onclick="closePopup()">Close</button>
        <button class="btn-primary"   onclick="window.location.href='../index.html'">
          Continue →
        </button>
      </div>

    </div>
  </div>

  <script>
  /* ── POPUP CONTROLS ── */
  function closePopup() {
      document.getElementById('attack-overlay').classList.remove('active');
  }

  /* Close on backdrop click */
  document.getElementById('attack-overlay').addEventListener('click', function(e) {
      if (e.target === this) closePopup();
  });

  /* ── CONFETTI (only fires if attack was detected) ── */
  <?php if ($attackDetected): ?>
  (function launchConfetti() {
      const colors = ['#e94560','#7c3aed','#22d3ee','#a78bfa','#f59e0b','#22c55e'];
      for (let i = 0; i < 90; i++) {
          setTimeout(function() {
              const dot       = document.createElement('div');
              dot.className   = 'confetti-dot';
              dot.style.cssText = [
                  'left:'       + (Math.random() * 100) + 'vw',
                  'top:-10px',
                  'background:' + colors[Math.floor(Math.random() * colors.length)],
                  'animation-duration:' + (1.2 + Math.random() * 2) + 's',
                  'width:'  + (6 + Math.random() * 6) + 'px',
                  'height:' + (6 + Math.random() * 6) + 'px'
              ].join(';');
              document.body.appendChild(dot);
              setTimeout(function() { dot.remove(); }, 3500);
          }, i * 30);
      }
  })();
  <?php endif; ?>
  </script>

  <script src="../js/main.js"></script>
</body>
</html>