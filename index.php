<?php
// index.php - Chatbot Page (Standalone version)
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HackTheShop - Security Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #1e3a8a;
        }
        #chat-container {
            max-width: 500px;
            height: 620px;
            margin: 30px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        #chat-header {
            background: #1e40af;
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 1.3em;
            font-weight: bold;
        }
        #chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            line-height: 1.5;
        }
        .message {
            margin-bottom: 15px;
            padding: 12px 16px;
            border-radius: 18px;
            max-width: 85%;
        }
        .user-message {
            background: #2563eb;
            color: white;
            margin-left: auto;
        }
        .bot-message {
            background: #e2e8f0;
            color: #1e2937;
        }
        #chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        #user-input {
            flex: 1;
            padding: 14px;
            border: 1px solid #94a3b8;
            border-radius: 25px;
            font-size: 1em;
            outline: none;
        }
        #send-btn {
            padding: 0 24px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
        }
        .quick-buttons {
            padding: 10px;
            background: #f1f5f9;
            text-align: center;
        }
        .quick-btn {
            margin: 5px;
            padding: 8px 14px;
            font-size: 0.95em;
            background: #dbeafe;
            border: 1px solid #93c5fd;
            border-radius: 20px;
            cursor: pointer;
        }
        .quick-btn:hover {
            background: #bfdbfe;
        }
    </style>
</head>
<body>

    <h1>HackTheShop - Web Security Lab</h1>
    
    <div id="chat-container">
        <div id="chat-header">
            🤖 Security Chatbot
        </div>
        
        <div id="chat-messages">
            <div class="message bot-message">
                Hello! I'm your Security Assistant.<br><br>
                You can ask me anything about:<br>
                • SQL Injection<br>
                • XSS (Cross-Site Scripting)<br>
                • How to protect against them<br><br>
                Try clicking the quick buttons below or type your question!
            </div>
        </div>

        <div class="quick-buttons">
            <button class="quick-btn" onclick="sendQuickMessage('Explain SQL Injection')">SQL Injection</button>
            <button class="quick-btn" onclick="sendQuickMessage('Explain XSS')">XSS Attack</button>
            <button class="quick-btn" onclick="sendQuickMessage('Give me a SQL Injection example')">SQLi Example</button>
            <button class="quick-btn" onclick="sendQuickMessage('Give me an XSS example')">XSS Example</button>
            <button class="quick-btn" onclick="sendQuickMessage('How to protect the website?')">Protection Tips</button>
        </div>

        <div id="chat-input-area">
            <input type="text" id="user-input" placeholder="Ask about SQLi, XSS, or security..." autocomplete="off">
            <button id="send-btn">Send</button>
        </div>
    </div>

<script>
// assets/js/chatbot.js embedded for simplicity
const chatMessages = document.getElementById('chat-messages');
const userInput = document.getElementById('user-input');
const sendBtn = document.getElementById('send-btn');

function addMessage(text, type) {
    const div = document.createElement('div');
    div.className = `message ${type}-message`;
    div.innerHTML = text;
    chatMessages.appendChild(div);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendMessage() {
    const message = userInput.value.trim();
    if (!message) return;

    addMessage(message, 'user');
    userInput.value = '';

    fetch('reply.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        addMessage(data.reply, 'bot');
    })
    .catch(() => {
        addMessage("Sorry, I couldn't connect. Is reply.php in the same folder?", 'bot');
    });
}

function sendQuickMessage(text) {
    userInput.value = text;
    sendMessage();
}

// Event listeners
sendBtn.addEventListener('click', sendMessage);
userInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});
</script>

</body>
</html>