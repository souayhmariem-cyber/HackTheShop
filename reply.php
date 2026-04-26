<?php
session_start();
header('Content-Type: application/json');

$message = strtolower(trim($_POST['message'] ?? ''));

if (!$message) {
    echo json_encode([
        "reply" => "💬 Please type a message first."
    ]);
    exit;
}

if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = 'general';
}

$mode = $_SESSION['mode'];

$securityWords = [
    'sql', 'sqli', 'xss', 'csrf', 'hack', 'hacker',
    'security', 'attack', 'vulnerability', 'inject',
    'cookie', 'session', 'password', 'bypass',
    'exploit', 'login', 'admin'
];

$shopWords = [
    'buy', 'price', 'product', 'cart', 'shop',
    'shipping', 'delivery', 'order', 'refund',
    'return', 'payment', 'stock', 'customer',
    'discount', 'promo', 'coupon', 'track'
];

$isSecurity = false;
$isShop = false;

foreach ($securityWords as $word) {
    if (str_contains($message, $word)) {
        $isSecurity = true;
        break;
    }
}

foreach ($shopWords as $word) {
    if (str_contains($message, $word)) {
        $isShop = true;
        break;
    }
}

if (str_contains($message, 'lab mode') || str_contains($message, 'security mode')) {
    $_SESSION['mode'] = 'security';
    echo json_encode([
        "reply" => "🔓 Security Lab Mode Activated.<br><br>Ask me about SQL Injection, XSS, sessions, cookies, or website vulnerabilities."
    ]);
    exit;
}

if (str_contains($message, 'shop mode')) {
    $_SESSION['mode'] = 'shop';
    echo json_encode([
        "reply" => "🛒 Shop Assistant Mode Activated.<br><br>Ask me about orders, shipping, products, refunds, or payments."
    ]);
    exit;
}

if (str_contains($message, 'reset')) {
    $_SESSION['mode'] = 'general';
    echo json_encode([
        "reply" => "♻️ Chat mode reset. I can now help with both shopping and security."
    ]);
    exit;
}

if ($isSecurity) {
    $_SESSION['mode'] = 'security';
}

if ($isShop) {
    $_SESSION['mode'] = 'shop';
}

$mode = $_SESSION['mode'];

$reply = "";

if (
    str_contains($message, 'hello') ||
    str_contains($message, 'hi') ||
    str_contains($message, 'hey')
) {
    $reply = "👋 Hello! Welcome to HackTheShop.<br><br>"
           . "I can help with:<br>"
           . "🛒 Orders / Products / Shipping<br>"
           . "🔐 Website Security / SQLi / XSS<br><br>"
           . "Type <b>lab mode</b> to enter hidden security mode.";
}

elseif ($mode === 'security') {

    if (str_contains($message, 'sql')) {
        $reply = "🔐 <b>SQL Injection</b> happens when user input is inserted into SQL queries.<br><br>"
               . "<b>Example payload:</b><br>"
               . "<code>' OR '1'='1</code><br><br>"
               . "<b>Protection:</b> Use prepared statements with PDO.";
    }

    elseif (str_contains($message, 'xss')) {
        $reply = "🔐 <b>XSS (Cross-Site Scripting)</b> injects JavaScript into pages.<br><br>"
               . "<b>Example:</b><br>"
               . "<code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code><br><br>"
               . "<b>Protection:</b> Use htmlspecialchars() and CSP.";
    }

    elseif (str_contains($message, 'cookie') || str_contains($message, 'session')) {
        $reply = "🍪 <b>Cookie & Session Security:</b><br>"
               . "• Use HttpOnly cookies<br>"
               . "• Use Secure flag<br>"
               . "• Regenerate session ID after login";
    }

    elseif (str_contains($message, 'password')) {
        $reply = "🔑 Store passwords using:<br><br><code>password_hash()</code><br><br>"
               . "Never store plain text passwords.";
    }

    else {
        $reply = "🔓 Security Lab Mode active.<br><br>"
               . "Try asking:<br>"
               . "• Explain SQL Injection<br>"
               . "• XSS example<br>"
               . "• How to secure login page";
    }
}

elseif ($mode === 'shop') {

    if (str_contains($message, 'shipping') || str_contains($message, 'delivery')) {
        $reply = "📦 Standard shipping takes 3–5 business days.";
    }

    elseif (str_contains($message, 'refund') || str_contains($message, 'return')) {
        $reply = "↩️ Returns are accepted within 30 days.";
    }

    elseif (str_contains($message, 'payment')) {
        $reply = "💳 We accept Visa, Mastercard, PayPal, and bank transfer.";
    }

    elseif (str_contains($message, 'track') || str_contains($message, 'order')) {
        $reply = "📍 Please send your order ID to track your shipment.";
    }

    elseif (str_contains($message, 'discount') || str_contains($message, 'promo')) {
        $reply = "🏷️ Use code <b>WELCOME10</b> for 10% off your first order.";
    }

    else {
        $reply = "🛒 Shop Assistant Mode active.<br><br>"
               . "Ask me about products, shipping, payments, refunds, or orders.";
    }
}

else {
    $reply = "🤖 I'm your HackTheShop assistant.<br><br>"
           . "I can help with:<br>"
           . "🛒 Shopping support<br>"
           . "🔐 Website security topics<br><br>"
           . "Try asking anything!";
}

echo json_encode([
    "reply" => $reply
]);
?>