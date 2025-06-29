<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];

// Delete messages
$db->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?")->execute([$user_id, $user_id]);

// Delete friend requests
$db->prepare("DELETE FROM friends WHERE sender_id = ? OR receiver_id = ?")->execute([$user_id, $user_id]);

// Delete user
$db->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);

session_destroy();
header("Location: register.php?deleted=1");
exit;
?>
