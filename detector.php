<?php
session_start();
header('Content-Type: application/json');

$attacks = [
    'sqli' => [
        'name'     => 'SQL Injection — Authentication Bypass',
        'emoji'    => '💉',
        'severity' => 'CRITICAL',
        'color'    => '#e94560',
        'what'     => "You injected SQL into the login form. The query became: SELECT * FROM users WHERE email='' OR '1'='1'--' which is always true, returning the first user with no password needed.",
        'why'      => 'An attacker can log in as any user including admin without knowing any password. One payload compromises every account.',
        'fix'      => 'Use prepared statements: $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?"); $stmt->execute([$email, $password]);',
        'impact'   => 'Full account takeover · Admin access · Complete data breach · OWASP Top 10 #3'
    ],
    'xss_reflected' => [
        'name'     => 'Reflected XSS — Search Bar',
        'emoji'    => '🪞',
        'severity' => 'HIGH',
        'color'    => '#f59e0b',
        'what'     => 'Your script tag was sent to the server inside the search query and reflected back into the HTML without sanitization. The browser executed it immediately.',
        'why'      => 'An attacker crafts a malicious URL and sends it to a victim. One click steals their session cookie or redirects them to a fake login page.',
        'fix'      => 'Always escape output: echo htmlspecialchars($input, ENT_QUOTES, "UTF-8"); — this converts < > into harmless &lt; &gt;',
        'impact'   => 'Session hijacking · Cookie theft · Phishing · Credential harvesting'
    ],
    'xss_stored' => [
        'name'     => 'Stored XSS — Comments Section',
        'emoji'    => '💾',
        'severity' => 'HIGH',
        'color'    => '#f59e0b',
        'what'     => 'Your script was saved in the database and is now served to every visitor who loads this page. The attack persists without any further action from you.',
        'why'      => 'Far more dangerous than reflected XSS. No need to trick users into clicking a link — every visitor is automatically attacked.',
        'fix'      => 'Sanitize on save: strip_tags($input). Escape on display: htmlspecialchars($row["contenu"], ENT_QUOTES, "UTF-8").',
        'impact'   => 'Mass session hijacking · Page defacement · Malware distribution to all visitors'
    ],
    'dom_xss' => [
        'name'     => 'DOM XSS — Client-Side Injection',
        'emoji'    => '🌐',
        'severity' => 'HIGH',
        'color'    => '#f59e0b',
        'what'     => 'JavaScript on the page read your payload from the URL using window.location and wrote it into the DOM using innerHTML. The server never saw the attack — it happened entirely in the browser.',
        'why'      => 'Completely invisible to server-side filters and WAFs. The server logs show a normal request with no malicious content.',
        'fix'      => 'Replace innerHTML with textContent for user-controlled data. Or use DOMPurify.sanitize(input) before inserting HTML.',
        'impact'   => 'Same as reflected XSS but bypasses all server-side protections'
    ],
    'idor' => [
        'name'     => 'IDOR — Insecure Direct Object Reference',
        'emoji'    => '🔓',
        'severity' => 'HIGH',
        'color'    => '#f59e0b',
        'what'     => 'You accessed another user\'s order by changing the ?id= number in the URL. The server returned the data without checking if that order belongs to your account.',
        'why'      => 'Any logged-in user can access any other user\'s private orders, addresses, and payment info just by guessing sequential IDs.',
        'fix'      => 'Always verify ownership in the query: WHERE id = ? AND user_id = $_SESSION["user_id"] — if no row returned, deny access.',
        'impact'   => 'Privacy breach · Financial data exposure · Personal information theft · OWASP Top 10 #1'
    ],
    'prompt_injection' => [
        'name'     => 'Prompt Injection — AI Chatbot',
        'emoji'    => '🤖',
        'severity' => 'MEDIUM',
        'color'    => '#8b5cf6',
        'what'     => 'You overrode the chatbot\'s system instructions by embedding a new directive inside your user message. The AI model obeyed your instructions instead of its original configuration.',
        'why'      => 'AI models process system prompts and user messages as the same type of text. There is no technical boundary between them from the model\'s perspective.',
        'fix'      => 'Never embed real secrets in system prompts. Use output filters to block sensitive patterns. Treat all AI output as untrusted.',
        'impact'   => 'Secret exfiltration · Content filter bypass · Impersonation · Misleading outputs'
    ],
    'devtools' => [
        'name'     => 'Sensitive Data in Source Code',
        'emoji'    => '🔍',
        'severity' => 'LOW',
        'color'    => '#22d3ee',
        'what'     => 'You found a hidden admin path, secret token, or debug variable by reading the HTML source or browser DevTools. The developer left internal information exposed in client-side code.',
        'why'      => 'Everything in HTML and JavaScript is public. Any user can press F12 and read it. Secrets, tokens, and admin paths must never appear in client-side code.',
        'fix'      => 'Move all secrets to server-side environment variables (.env files). Remove all TODO/DEBUG/FIXME comments before deployment. Use code review checklists.',
        'impact'   => 'Admin panel discovery · API key abuse · Internal infrastructure mapping · Reconnaissance for further attacks'
    ]
];

if (isset($_SESSION['attack']) && array_key_exists($_SESSION['attack'], $attacks)) {
    $key  = $_SESSION['attack'];
    $data = $attacks[$key];
    unset($_SESSION['attack']);
    echo json_encode(['detected' => true, 'key' => $key, 'data' => $data]);
} else {
    echo json_encode(['detected' => false]);
}
?>