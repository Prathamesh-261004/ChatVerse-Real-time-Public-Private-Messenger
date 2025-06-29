<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Get pending friend requests where current user is the receiver
$pending = $db->prepare("SELECT f.id, u.username, u.dp FROM friends f
    JOIN users u ON u.id = f.sender_id
    WHERE f.receiver_id = ? AND f.status = 'pending'");
$pending->execute([$user_id]);
$requests = $pending->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Friend Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family:sans-serif; background:#111; color:#eee; padding:30px">
  <h2>👥 Friend Requests</h2>

  <?php if (count($requests) === 0): ?>
    <p style="color:#ccc;">No pending requests.</p>
  <?php endif; ?>

  <?php foreach ($requests as $r): ?>
    <div style="margin:15px 0; padding:15px; background:#222; border-radius:10px;">
      <img src="<?= htmlspecialchars($r['dp']) ?>" style="width:40px; height:40px; border-radius:50%; vertical-align:middle">
      <strong style="margin-left:10px"><?= htmlspecialchars($r['username']) ?></strong>
      <a href="respond_request.php?action=accept&id=<?= $r['id'] ?>" style="color:lime; margin-left:15px; font-size:16px;">✅ Accept</a>
      <a href="respond_request.php?action=reject&id=<?= $r['id'] ?>" style="color:orangered; margin-left:10px; font-size:16px;">❌ Reject</a>
    </div>
  <?php endforeach; ?>

  <br>
  <a href="public_67.php" style="color:#00e5ff; text-decoration:none;">⬅ Back to Chat</a>
</body>
</html>
