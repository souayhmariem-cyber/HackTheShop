<?php
session_start();
require 'db_connection.php';

// ─────────────────────────────────────────────
// INTENTIONALLY WEAK AUTH — Educational demo
// Token is discoverable via F12 / View Source
// Real admin panels must never work this way
// ─────────────────────────────────────────────
$token = $_GET['token'] ?? '';
$validToken = 'HS_DEBUG_2025';

if ($token !== $validToken) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>403 — Forbidden</title>
        <link rel="stylesheet" href="style.css">
        <style>
            .error-page {
                display:flex; flex-direction:column;
                align-items:center; justify-content:center;
                min-height:60vh; font-family:'Courier New',monospace;
                color:#e2d9f3; text-align:center; gap:16px;
            }
            .error-code { font-size:72px; color:#e94560; font-weight:bold; }
            .error-msg  { font-size:16px; color:#a78bfa; }
            .error-hint { font-size:12px; color:#534AB7; margin-top:8px; }
        </style>
    </head>
    <body>
        <div class="error-page">
            <div class="error-code">403</div>
            <div class="error-msg">Access Denied — Admin Panel</div>
            <div class="error-hint">
                <!-- Hint for students: try checking the page source of index.html 🔍 -->
                Hint: the key is somewhere in the source code...
            </div>
            <a href="index.html" style="color:#7c3aed;font-size:13px;">← Back to Shop</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ── If token is correct, flag DevTools discovery attack ──
$_SESSION['attack'] = 'devtools';

// ── Fetch data from DB ──
try {
    $users  = $pdo->query("SELECT id, nom, email, role, created_at FROM users ORDER BY id ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    $orders = $pdo->query("SELECT o.id, o.user_id, u.nom, o.total, o.status, o.created_at
                            FROM orders o
                            LEFT JOIN users u ON o.user_id = u.id
                            ORDER BY o.id ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    $comments = $pdo->query("SELECT c.id, c.auteur, c.contenu, c.created_at
                              FROM comments c
                              ORDER BY c.id DESC LIMIT 20")
                     ->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users    = [];
    $orders   = [];
    $comments = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HackTheShop — Admin Debug Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── ADMIN PANEL STYLES ── */
        body {
            background: #080810;
            font-family: 'Courier New', monospace;
            color: #e2d9f3;
            margin: 0;
            padding: 0;
        }

        .admin-wrap {
            max-width: 1000px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        /* Header */
        .admin-header {
            border: 1px solid #e94560;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 28px;
            background: #13131f;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .admin-header h1 {
            color: #e94560;
            font-size: 20px;
            margin: 0;
        }

        .admin-header p {
            color: #a78bfa;
            font-size: 12px;
            margin: 6px 0 0;
        }

        .admin-token {
            background: #0d0d14;
            border: 1px solid #3b2f6e;
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 11px;
            color: #534AB7;
        }

        /* Warning banner */
        .vuln-banner {
            background: #1a0a00;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 28px;
            font-size: 12px;
            color: #f59e0b;
            line-height: 1.6;
        }

        .vuln-banner strong { color: #fbbf24; }

        /* Section cards */
        .admin-section {
            background: #13131f;
            border: 1px solid #3b2f6e;
            border-radius: 12px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            padding: 14px 20px;
            border-bottom: 1px solid #3b2f6e;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header h2 {
            font-size: 14px;
            margin: 0;
            letter-spacing: 1px;
        }

        .section-header .count {
            font-size: 11px;
            color: #534AB7;
        }

        .cyan  { color: #22d3ee; }
        .green { color: #22c55e; }
        .purple{ color: #a78bfa; }

        /* Tables */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .admin-table th {
            padding: 10px 16px;
            text-align: left;
            color: #534AB7;
            font-size: 10px;
            letter-spacing: 1px;
            border-bottom: 1px solid #1e1a2e;
            background: #0d0d14;
        }

        .admin-table td {
            padding: 10px 16px;
            border-bottom: 1px solid #1a1a2e;
            color: #cbd5e1;
            vertical-align: top;
        }

        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td     { background: #0d0d14; }

        /* Role badges */
        .role-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .role-admin { background: #e9456022; color: #e94560; border: 1px solid #e94560; }
        .role-user  { background: #7c3aed22; color: #a78bfa; border: 1px solid #7c3aed; }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
        }

        .status-pending   { background: #f59e0b22; color: #f59e0b; border: 1px solid #f59e0b; }
        .status-completed { background: #22c55e22; color: #22c55e; border: 1px solid #22c55e; }
        .status-cancelled { background: #e9456022; color: #e94560; border: 1px solid #e94560; }

        /* Comment content */
        .comment-content {
            max-width: 300px;
            word-break: break-word;
            color: #f87171;  /* red to signal unsafe content */
            font-size: 12px;
        }

        /* Empty state */
        .empty-state {
            padding: 24px;
            text-align: center;
            color: #534AB7;
            font-size: 13px;
        }

        /* Footer nav */
        .admin-footer {
            text-align: center;
            padding: 20px;
            color: #534AB7;
            font-size: 12px;
        }

        .admin-footer a {
            color: #7c3aed;
            text-decoration: none;
            margin: 0 12px;
        }

        .admin-footer a:hover { color: #a78bfa; }

        /* Stats row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #13131f;
            border: 1px solid #3b2f6e;
            border-radius: 10px;
            padding: 18px 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 10px;
            color: #534AB7;
            letter-spacing: 1px;
        }

        @media (max-width: 600px) {
            .stats-row { grid-template-columns: 1fr; }
            .admin-table { font-size: 11px; }
            .admin-table th,
            .admin-table td { padding: 8px 10px; }
        }
    </style>
</head>
<body>

<div class="admin-wrap">

    <!-- HEADER -->
    <div class="admin-header">
        <div>
            <h1>⚠️ Admin Debug Panel</h1>
            <p>
                You discovered this panel by finding the token in the page source.<br>
                This is the <strong style="color:#e94560;">Sensitive Data Exposure</strong>
                + <strong style="color:#e94560;">Security Misconfiguration</strong> vulnerability.
            </p>
        </div>
        <div class="admin-token">
            token: <?php echo htmlspecialchars($token); ?>
        </div>
    </div>

    <!-- EDUCATIONAL WARNING -->
    <div class="vuln-banner">
        🎓 <strong>What you just exploited:</strong>
        This admin panel is protected only by a secret token embedded in the HTML source code
        (<code>index.html</code> and <code>main.js</code>).
        Any user who presses <strong>F12 → Sources</strong> or <strong>View Page Source</strong>
        can find it. Real admin panels must use server-side session checks with role verification —
        never URL tokens.
    </div>

    <!-- STATS -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-number cyan"><?php echo count($users); ?></div>
            <div class="stat-label">TOTAL USERS</div>
        </div>
        <div class="stat-card">
            <div class="stat-number green"><?php echo count($orders); ?></div>
            <div class="stat-label">TOTAL ORDERS</div>
        </div>
        <div class="stat-card">
            <div class="stat-number purple"><?php echo count($comments); ?></div>
            <div class="stat-label">COMMENTS</div>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="admin-section">
        <div class="section-header">
            <h2 class="cyan">👥 Users</h2>
            <span class="count"><?php echo count($users); ?> records</span>
        </div>

        <?php if (empty($users)): ?>
            <div class="empty-state">No users found in database.</div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>ROLE</th>
                    <th>REGISTERED</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="color:#534AB7;"><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nom']); ?></td>
                    <td style="color:#22d3ee;"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="role-badge <?php echo $u['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                            <?php echo strtoupper($u['role']); ?>
                        </span>
                    </td>
                    <td style="color:#534AB7;"><?php echo $u['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ORDERS TABLE -->
    <div class="admin-section">
        <div class="section-header">
            <h2 class="green">📦 Orders</h2>
            <span class="count"><?php echo count($orders); ?> records</span>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">No orders found in database.</div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#ORDER</th>
                    <th>USER</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                    <th>DATE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td style="color:#534AB7;">#<?php echo $o['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($o['nom'] ?? 'Unknown'); ?>
                        <span style="color:#534AB7;font-size:10px;">
                            (user #<?php echo $o['user_id']; ?>)
                        </span>
                    </td>
                    <td style="color:#22c55e;"><?php echo number_format($o['total'], 2); ?>€</td>
                    <td>
                        <?php
                        $status = strtolower($o['status'] ?? 'pending');
                        $cls    = 'status-pending';
                        if ($status === 'completed') $cls = 'status-completed';
                        if ($status === 'cancelled') $cls = 'status-cancelled';
                        ?>
                        <span class="status-badge <?php echo $cls; ?>">
                            <?php echo strtoupper($status); ?>
                        </span>
                    </td>
                    <td style="color:#534AB7;"><?php echo $o['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- COMMENTS TABLE -->
    <div class="admin-section">
        <div class="section-header">
            <h2 class="purple">💬 Recent Comments</h2>
            <span class="count">last <?php echo count($comments); ?> records</span>
        </div>

        <?php if (empty($comments)): ?>
            <div class="empty-state">No comments found. Try the Stored XSS challenge first!</div>
        <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>AUTHOR</th>
                    <th>CONTENT (RAW — unsafe)</th>
                    <th>DATE</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $c): ?>
                <tr>
                    <td style="color:#534AB7;"><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['auteur']); ?></td>
                    <td>
                        <!--
                            Showing raw content here to demonstrate what is stored.
                            In a real admin panel this would still be escaped.
                        -->
                        <div class="comment-content">
                            <?php echo htmlspecialchars($c['contenu']); ?>
                        </div>
                    </td>
                    <td style="color:#534AB7;"><?php echo $c['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <div class="admin-footer">
        <a href="index.html">← Back to Shop</a>
        <a href="login.html">Login Page</a>
        <a href="products.html">Products</a>
        <br><br>
        © 2025 HackTheShop — Educational Cybersecurity Project
    </div>

</div>

<script src="popup.js"></script>
</body>
</html>