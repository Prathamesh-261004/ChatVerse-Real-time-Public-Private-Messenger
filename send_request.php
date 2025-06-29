<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) die("Login required");

$from = $_SESSION['user']['id'];
$to = $_GET['to'] ?? 0;

// Prevent duplicate
$check = $db->prepare("SELECT * FROM friends WHERE 
    (sender_id = ? AND receiver_id = ?) OR 
    (sender_id = ? AND receiver_id = ?)");
$check->execute([$from, $to, $to, $from]);

if ($check->rowCount() > 0) {
    echo "<p style='color:white; background:#333; padding:15px; text-align:center;'>
        Request already exists. Redirecting to Public Chat...
    </p>";
    echo "<script>
        setTimeout(() => window.location.href = 'friend_requests.php', 2000);
    </script>";
    exit;
}

// Send new request
$stmt = $db->prepare("INSERT INTO friends (sender_id, receiver_id) VALUES (?, ?)");
$stmt->execute([$from, $to]);

// 🔁 Redirect to friend_requests page
header("Location: friend_requests.php");
exit;
