<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) header("Location: login.php");
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>🌐 Public Chat | ChatVerse</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      display: flex;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #eee;
      overflow: hidden;
    }
    .hamburger {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      font-size: 26px;
      background: #00e5ff;
      color: #000;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      z-index: 1100;
    }
    .hamburger.shifted { left: 280px; }
    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1050;
    }
    .overlay.active { display: block; }
    .sidebar {
      width: 260px;
      background: #121420;
      color: #eee;
      padding: 20px;
      overflow-y: auto;
      z-index: 1101;
      flex-shrink: 0;
      transition: transform 0.3s ease;
    }
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        transform: translateX(-100%);
      }
      .sidebar.open { transform: translateX(0); }
      .hamburger { display: block; }
    }
    .sidebar h3 {
      text-align: center;
      color: #00e5ff;
      margin-bottom: 15px;
    }
    .sidebar img {
      display: block;
      margin: 0 auto 20px;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      border: 2px solid #00e5ff;
    }
    .sidebar a {
      color: #00e5ffcc;
      text-decoration: none;
      display: flex;
      align-items: center;
      margin: 10px 0;
    }
    .sidebar a img.dp {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      margin-right: 12px;
    }
    .chatbox {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 25px 30px;
      background: #1f1f2e;
    }
    .chatbox h3 {
      text-align: center;
      font-size: 1.8em;
      color: #00e5ff;
    }
    .messages {
      flex: 1;
      background: #262636;
      padding: 20px;
      border-radius: 15px;
      overflow-y: auto;
    }
    .msg {
      margin: 12px 0;
      padding: 12px 18px;
      border-radius: 18px;
      background: #003f5c;
      color: #aaffff;
      max-width: 75%;
      position: relative;
    }
    .msg .sender {
      font-weight: bold;
      color: #00e5ff;
    }
    form {
      margin-top: 20px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    input[type="text"], input[type="file"] {
      flex: 1 1 60%;
      padding: 12px;
      border-radius: 12px;
      border: 1px solid #00e5ff88;
      background: #121420;
      color: #eee;
    }
  button.floating-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 14px 24px;
  background: #00e5ff;
  color: #000;
  border: none;
  border-radius: 50px;
  font-size: 16px;
  font-weight: bold;
  box-shadow: 0 0 15px #00e5ff99;
  cursor: pointer;
  z-index: 1000;
  animation: floatUpDown 2.5s ease-in-out infinite, glowPulse 3s ease-in-out infinite;
  transition: transform 0.3s, box-shadow 0.3s;
}

button.floating-btn:hover {
  transform: scale(1.08);
  box-shadow: 0 0 25px #00e5ff;
}

@keyframes floatUpDown {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

@keyframes glowPulse {
  0%, 100% { box-shadow: 0 0 12px #00e5ff88; }
  50% { box-shadow: 0 0 20px #00e5ffcc; }
}

@media (max-width: 600px) {
  button.floating-btn {
    padding: 12px 20px;
    font-size: 14px;
    bottom: 16px;
    right: 16px;
  }
}
.delete-account-btn {
  position: fixed;
  bottom: 20px;
  left: 20px;
  padding: 12px 20px;
  background: #ff0033;
  color: white;
  text-decoration: none;
  border-radius: 40px;
  font-size: 15px;
  font-weight: bold;
  box-shadow: 0 0 10px #ff003388;
  z-index: 1000;
  transition: 0.3s;
  animation: pulseRed 2s infinite;
}

.delete-account-btn:hover {
  background: #cc002b;
  box-shadow: 0 0 20px #ff0033aa;
  transform: scale(1.05);
}

@keyframes pulseRed {
  0% { box-shadow: 0 0 10px #ff003366; }
  50% { box-shadow: 0 0 20px #ff0033cc; }
  100% { box-shadow: 0 0 10px #ff003366; }
}

@media (max-width: 600px) {
  .delete-account-btn {
    bottom: 15px;
    left: 15px;
    font-size: 14px;
    padding: 10px 16px;
  }
}


  </style>
</head>
<body>
<div class="hamburger" id="hamburger" onclick="toggleSidebar()">☰</div>
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
  <h3><?= htmlspecialchars($user['username']) ?></h3>
  <img src="<?= htmlspecialchars($user['dp']) ?>" class="dp" alt="Me" />
  <a href="logout.php">🚪 Logout</a>
  <h4>Private Chats</h4>
  <a href="javascript:void(0)" class="delete-account-btn" onclick="confirmDelete()">🗑️ Delete My Account</a>

  <?php
    $users = $db->query("SELECT * FROM users WHERE id != {$user['id']}")->fetchAll();
    foreach ($users as $u) {
        echo "<a href='67_with.php?user_id={$u['id']}' onclick='closeSidebar()'>
                <img src='".htmlspecialchars($u['dp'])."' class='dp'> ".htmlspecialchars($u['username'])."
              </a>";
    }
    
  ?>
</div>

<div class="chatbox">
  <h3>🌐 Public Chat Room</h3>
  <div id="messages" class="messages"></div>
  <form id="send-form" method="POST" enctype="multipart/form-data">
    <input type="text" name="message" id="msg" placeholder="Type your message..." autocomplete="off" />
    <input type="file" name="file" accept="image/*,video/*,audio/*,.pdf" />
    <button class="floating-btn" type="submit">Send</button>
  </form>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  const hamburger = document.getElementById('hamburger');
  const overlay = document.getElementById('overlay');
  const msgBox = document.getElementById("messages");

  function toggleSidebar() {
    sidebar.classList.toggle('open');
    hamburger.classList.toggle('shifted');
    overlay.classList.toggle('active');
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    hamburger.classList.remove('shifted');
    overlay.classList.remove('active');
  }

  async function loadMessages() {
    const res = await fetch("fetch_67.php?scope=public");
    const html = await res.text();
    msgBox.innerHTML = html;
    msgBox.scrollTop = msgBox.scrollHeight;
  }

  setInterval(loadMessages, 1000);
  loadMessages();

  document.getElementById("send-form").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append("receiver_id", "");
    await fetch("send_67.php", {
      method: "POST",
      body: formData
    });
    this.reset();
    loadMessages();
    closeSidebar();
  });
</script>
<script>
function confirmDelete() {
  if (confirm("Are you sure you want to permanently delete your account? This cannot be undone.")) {
    window.location.href = "delete_account.php";
  }
}
</script>

</body>
</html>
