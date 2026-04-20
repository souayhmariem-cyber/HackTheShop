<!-- Save this as index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simple Chatbot</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.chat-container {
    width: 400px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chat-box {
    height: 500px;
    padding: 15px;
    overflow-y: auto;
    border-bottom: 1px solid #ddd;
}

.message {
    padding: 10px;
    margin: 8px 0;
    border-radius: 8px;
    max-width: 75%;
}

.user {
    background: #007bff;
    color: white;
    margin-left: auto;
}

.bot {
    background: #e4e6eb;
    color: black;
}

.input-area {
    display: flex;
}

input {
    flex: 1;
    padding: 12px;
    border: none;
    outline: none;
}

button {
    padding: 12px 20px;
    border: none;
    background: #007bff;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}
</style>
</head>

<body>

<div class="chat-container">
    <div class="chat-box" id="chatBox"></div>

    <div class="input-area">
        <input type="text" id="userInput" placeholder="Type a message...">
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
function sendMessage() {
    let input = document.getElementById("userInput");
    let message = input.value.trim();

    if(message === "") return;

    let chatBox = document.getElementById("chatBox");

    // User message
    chatBox.innerHTML += `<div class="message user">${message}</div>`;

    // Send to PHP
    fetch("reply.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(data => {
        chatBox.innerHTML += `<div class="message bot">${data}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    });

    input.value = "";
}
</script>

</body>
</html>