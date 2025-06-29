<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) header("Location: login.php");
$user = $_SESSION['user'];
$receiver_id = $_GET['user_id'] ?? 0;

// Fetch receiver
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$receiver_id]);
$receiver = $stmt->fetch();
if (!$receiver) die("User not found.");

// Check if friendship exists
$stmt = $db->prepare("SELECT * FROM friends WHERE 
  ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
  AND status = 'accepted'");
$stmt->execute([$user['id'], $receiver_id, $receiver_id, $user['id']]);
$friendship = $stmt->fetch();

if (!$friendship) {
    echo "<p style='color:white; text-align:center; margin-top:20px;'>
      You are not friends with <strong>" . htmlspecialchars($receiver['username']) . "</strong> yet. 
      <br><a href='send_request.php?to=$receiver_id' style='color:#00e5ff; font-size:18px;'>📨 Send Friend Request</a></p>";
    exit;
}

// Count pending friend requests
$pendingCount = $db->prepare("SELECT COUNT(*) FROM friends WHERE receiver_id = ? AND status = 'pending'");
$pendingCount->execute([$user['id']]);
$pendingCount = $pendingCount->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat with <?= htmlspecialchars($receiver['username']) ?></title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #eee;
      height: 100vh;
      overflow: hidden;
    }
    * { box-sizing: border-box; }
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
      z-index: 1002;
      transition: left 0.3s ease;
      box-shadow: 0 0 10px #00e5ff8c;
    }
    .hamburger.shifted { left: 275px; }
    .overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
    }
    .overlay.active { display: block; }

    .container { display: flex; height: 100vh; }

    .sidebar {
      width: 260px;
      background: #121420;
      color: #eee;
      padding: 20px;
      overflow-y: auto;
      z-index: 1001;
    }

    .sidebar img {
      width: 64px; height: 64px;
      border-radius: 50%;
      border: 2px solid #00e5ff;
      margin: 15px 0;
      cursor: pointer;
    }

    .sidebar a {
      display: block;
      color: #00e5ff;
      text-decoration: none;
      margin: 10px 0;
      font-size: 16px;
    }

    .sidebar a:hover { color: #fff; }

    .chatbox {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 20px;
      background: #1f1f2e;
    }

    .messages {
      flex: 1;
      background: #262636;
      padding: 15px;
      border-radius: 12px;
      overflow-y: auto;
      box-shadow: inset 0 0 8px #00000088;
    }

    .msg {
      padding: 10px 14px;
      margin: 10px 0;
      border-radius: 14px;
      max-width: 80%;
      font-size: 14px;
      position: relative;
      animation: fadeIn 0.3s ease;
      word-wrap: break-word;
    }

    .msg.you {
      background: #003f5c;
      color: #aaffff;
      align-self: flex-end;
      border: 1px solid #00e5ff;
    }

    .msg.other {
      background: #3d1f3c;
      color: #ffdede;
      align-self: flex-start;
      border: 1px solid #f48fb1;
    }

    .msg .info {
      font-size: 11px;
      margin-top: 3px;
      color: #ccc;
      text-align: right;
    }

    .send-form {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 15px;
    }

    .send-form input, .send-form button {
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #555;
      background: #111;
      color: #eee;
    }

    .send-form button {
      background: #00e5ff;
      color: black;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 0 10px #00e5ff8c;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1003;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.8);
    }

    .modal-content {
      margin: 10% auto;
      width: 300px;
      background: #111;
      border-radius: 12px;
      position: relative;
      padding: 10px;
    }

    .close-modal {
      position: absolute;
      top: 8px;
      right: 12px;
      font-size: 22px;
      color: #ccc;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .sidebar { position: fixed; left: -100%; height: 100%; }
      .sidebar.open { left: 0; }
      .hamburger { display: block; }
      .chatbox { padding-top: 60px; }

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

  </style>
</head>
<body>

<div class="hamburger" id="hamburger" onclick="toggleSidebar()">☰</div>
<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<div class="container">
  <div class="sidebar" id="sidebar">
    <h3><?= htmlspecialchars($user['username']) ?></h3>
    <img src="<?= htmlspecialchars($user['dp']) ?>" onclick="showModal('<?= htmlspecialchars($user['dp']) ?>')">
    <a href="logout.php">🚪 Logout</a>
    <a href="public_67.php">🌐 Public Chat</a>
    <a href="friend_requests.php" style="font-size: 18px; font-weight:bold;">
      👥 Friend Requests
      <?= $pendingCount > 0 ? "<span style='color:red; font-size:22px;'>🔔</span>" : "" ?>
    </a>

    <h4 style="margin-top:20px;">Private Chats</h4>
    <?php
    $users = $db->query("SELECT * FROM users WHERE id != {$user['id']}")->fetchAll();
    foreach ($users as $u) {
        $dp = htmlspecialchars($u['dp']);
        echo "<div class='user-link'>
                <img src='$dp' onclick=\"showModal('$dp')\">
                <a href='67_with.php?user_id={$u['id']}' onclick='closeSidebar()'>" . htmlspecialchars($u['username']) . "</a>
              </div>";
    }
    ?>
  </div>

  <div class="chatbox">
    <h3>Chat with: <?= htmlspecialchars($receiver['username']) ?></h3>
    <div id="messages" class="messages"></div>
    <form id="send-form" method="POST" enctype="multipart/form-data" class="send-form">
      <input type="hidden" name="receiver_id" value="<?= $receiver['id'] ?>">
      <input type="text" name="message" id="msg" placeholder="Type a message..." required>
      <input type="file" name="file" accept="image/*,video/*,audio/*,.pdf">
       <button class="floating-btn" >Send</button>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="dpModal">
  <div class="modal-content">
    <span class="close-modal" onclick="document.getElementById('dpModal').style.display='none'">×</span>
    <img id="modal-img" src="" alt="User DP">
  </div>
</div>

<script>
function loadMessages() {
  fetch("fetch_67.php?scope=private&receiver_id=<?= $receiver['id'] ?>")
    .then(res => res.text())
    .then(html => {
      document.getElementById("messages").innerHTML = html;
      document.getElementById("messages").scrollTop = document.getElementById("messages").scrollHeight;
    });
}
setInterval(loadMessages, 1000);
loadMessages();

document.getElementById("send-form").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch("send_67.php", { method: "POST", body: formData }).then(() => {
    document.getElementById("msg").value = "";
    this.querySelector('[type=file]').value = "";
  });
});

function showModal(src) {
  document.getElementById("modal-img").src = src;
  document.getElementById("dpModal").style.display = "block";
}
window.onclick = function(e) {
  if (e.target === document.getElementById("dpModal")) {
    document.getElementById("dpModal").style.display = "none";
  }
};

function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
  document.getElementById("overlay").classList.toggle("active");
  document.getElementById("hamburger").classList.toggle("shifted");
}
function closeSidebar() {
  document.getElementById("sidebar").classList.remove("open");
  document.getElementById("overlay").classList.remove("active");
  document.getElementById("hamburger").classList.remove("shifted");
}
function deleteMsg(id) {
  if (confirm("Delete this message?")) {
    fetch("delete_67.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "message_id=" + id
    })
    .then(res => res.text())
    .then(res => {
      if (res.trim() === "deleted") {
        loadMessages(); // Reload messages
      } else {
        alert("You can only delete your own messages.");
      }
    });
  }
}

</script>
</body>
</html>
