<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['club_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'];
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Club Chat - Book Club</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f9f9ff;
      padding: 30px;
    }
    .chat-container {
      max-width: 700px;
      margin: auto;
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .messages {
      height: 300px;
      overflow-y: scroll;
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 10px;
      background: #f5f5f5;
    }
    .message {
      margin-bottom: 10px;
    }
    .message strong {
      color: #2f2f4f;
    }
    .input-area {
      display: flex;
      gap: 10px;
    }
    .input-area input {
      flex-grow: 1;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .input-area button {
      padding: 10px 20px;
      border: none;
      background: #2C2F7F;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }
    .input-area button:hover {
      background: #1a1d5c;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #2c2c54;
    }
  </style>
</head>
<body>

<div class="chat-container">
  <h2>💬 Club Chat</h2>
  <div class="messages" id="messages"></div>

  <div class="input-area">
    <input type="text" id="messageInput" placeholder="Type your message...">
    <button onclick="sendMessage()">Send</button>
  </div>

  <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<script>
function fetchMessages() {
  fetch('get_messages.php')
    .then(response => response.json())
    .then(data => {
      const msgBox = document.getElementById('messages');
      msgBox.innerHTML = '';
      data.reverse().forEach(msg => {
        const msgEl = document.createElement('div');
        msgEl.className = 'message';
        msgEl.innerHTML = `<strong>${msg.name}</strong>: ${msg.message} <small>(${msg.timestamp})</small>`;
        msgBox.appendChild(msgEl);
      });
      msgBox.scrollTop = msgBox.scrollHeight;
    });
}

function sendMessage() {
  const input = document.getElementById('messageInput');
  const message = input.value.trim();
  if (message === '') return;

  fetch('send_message.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `message=${encodeURIComponent(message)}`
  }).then(() => {
    input.value = '';
    fetchMessages();
  });
}

// Auto-refresh messages
setInterval(fetchMessages, 3000);
fetchMessages();
</script>

</body>
</html>
