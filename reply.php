<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

$message = strtolower(trim($_POST['message'] ?? ''));

if (!$message) {
    echo json_encode(["reply" => "Please type a question!"]);
    exit;
}

// ─── Keyword matching ───────────────────────────────────────────────

if (str_contains($message, 'sql') && str_contains($message, 'example')) {
    $reply = "<b>SQL Injection example:</b><br><br>"
           . "<b>Normal login query:</b><br>"
           . "<code>SELECT * FROM users WHERE username='alice' AND password='1234'</code><br><br>"
           . "<b>Attacker types in the username field:</b><br>"
           . "<code>' OR '1'='1</code><br><br>"
           . "<b>The resulting query becomes:</b><br>"
           . "<code>SELECT * FROM users WHERE username='' OR '1'='1' AND password=''</code><br><br>"
           . "Because <code>'1'='1'</code> is always true, the attacker bypasses the login completely!";

} elseif (str_contains($message, 'xss') && str_contains($message, 'example')) {
    $reply = "<b>XSS example:</b><br><br>"
           . "<b>A website displays user input without filtering:</b><br>"
           . "<code>Hello, &lt;?php echo \$_GET['name']; ?&gt;</code><br><br>"
           . "<b>Attacker sends this URL:</b><br>"
           . "<code>page.php?name=&lt;script&gt;alert('Hacked!')&lt;/script&gt;</code><br><br>"
           . "The browser executes the script! An attacker could use this to steal cookies, "
           . "redirect users, or log keystrokes.";

} elseif (str_contains($message, 'sql')) {
    $reply = "<b>SQL Injection (SQLi)</b> is an attack where a hacker inserts malicious SQL code "
           . "into an input field to manipulate your database.<br><br>"
           . "<b>What can an attacker do?</b><br>"
           . "• Bypass login forms without a password<br>"
           . "• Dump the entire database (usernames, passwords, emails)<br>"
           . "• Delete or modify data<br>"
           . "• In some cases, take over the server<br><br>"
           . "<b>Where does it happen?</b> Any place where user input touches a database query — "
           . "login forms, search boxes, URL parameters.<br><br>"
           . "Try asking: <i>Give me a SQL Injection example</i>";

} elseif (str_contains($message, 'xss') || str_contains($message, 'cross-site') || str_contains($message, 'cross site scripting')) {
    $reply = "<b>XSS (Cross-Site Scripting)</b> is an attack where a hacker injects malicious "
           . "JavaScript into a web page that other users then execute in their browser.<br><br>"
           . "<b>Types of XSS:</b><br>"
           . "• <b>Stored XSS</b> — script is saved in the database, runs for every visitor<br>"
           . "• <b>Reflected XSS</b> — script is in the URL, runs when the victim clicks the link<br>"
           . "• <b>DOM XSS</b> — script manipulates the page directly via JavaScript<br><br>"
           . "<b>What can an attacker do?</b> Steal session cookies, redirect users to fake sites, "
           . "log keystrokes, or deface the page.<br><br>"
           . "Try asking: <i>Give me an XSS example</i>";

} elseif (str_contains($message, 'protect') || str_contains($message, 'prevent') || str_contains($message, 'defence') || str_contains($message, 'defense') || str_contains($message, 'secure')) {
    $reply = "<b>How to protect your website:</b><br><br>"
           . "🛡️ <b>Against SQL Injection:</b><br>"
           . "• Use <b>prepared statements</b> — never concatenate user input into queries<br>"
           . "• <code>\$stmt = \$pdo->prepare('SELECT * FROM users WHERE username = ?');</code><br>"
           . "• Validate and whitelist all user input<br>"
           . "• Use least-privilege database accounts<br><br>"
           . "🛡️ <b>Against XSS:</b><br>"
           . "• Always escape output with <code>htmlspecialchars()</code> before displaying user data<br>"
           . "• Use a Content Security Policy (CSP) header<br>"
           . "• Validate input server-side, never trust the client<br>"
           . "• Use <code>HttpOnly</code> and <code>Secure</code> flags on cookies";

} elseif (str_contains($message, 'prepared') || str_contains($message, 'pdo')) {
    $reply = "<b>Prepared Statements</b> are the #1 defence against SQL Injection.<br><br>"
           . "<b>Vulnerable code (NEVER do this):</b><br>"
           . "<code>\$query = \"SELECT * FROM users WHERE username = '\$username'\";</code><br><br>"
           . "<b>Safe code with PDO:</b><br>"
           . "<code>\$stmt = \$pdo->prepare('SELECT * FROM users WHERE username = ?');<br>"
           . "\$stmt->execute([\$username]);<br>"
           . "\$user = \$stmt->fetch();</code><br><br>"
           . "The user input is sent separately from the query — the database never treats it as SQL code.";

} elseif (str_contains($message, 'cookie') || str_contains($message, 'session')) {
    $reply = "<b>Session & Cookie Security:</b><br><br>"
           . "• Set <code>HttpOnly</code> — JavaScript cannot read the cookie (stops XSS cookie theft)<br>"
           . "• Set <code>Secure</code> — cookie is only sent over HTTPS<br>"
           . "• Regenerate session ID after login: <code>session_regenerate_id(true);</code><br>"
           . "• Set a short session timeout<br><br>"
           . "<b>Example:</b><br>"
           . "<code>setcookie('session', \$id, ['httponly' => true, 'secure' => true, 'samesite' => 'Strict']);</code>";

} elseif (str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'hey')) {
    $reply = "Hello! I'm your Security Assistant for the HackTheShop lab.<br><br>"
           . "Ask me about:<br>"
           . "• SQL Injection<br>"
           . "• XSS (Cross-Site Scripting)<br>"
           . "• How to protect your website<br>"
           . "• Prepared statements<br>"
           . "• Cookie & session security";

} else {
    $reply = "I'm not sure about that one. Try asking me about:<br><br>"
           . "• <b>SQL Injection</b> — how it works or give me an example<br>"
           . "• <b>XSS</b> — how it works or give me an example<br>"
           . "• <b>How to protect the website</b><br>"
           . "• <b>Prepared statements</b><br>"
           . "• <b>Cookie & session security</b>";
}

echo json_encode(["reply" => $reply]);