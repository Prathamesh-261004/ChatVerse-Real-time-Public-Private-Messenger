<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) exit;

$user_id = $_SESSION['user']['id'];
$message_id = $_POST['message_id'] ?? 0;

// Check if the message exists and belongs to this user
$stmt = $db->prepare("SELECT * FROM messages WHERE id = ? AND sender_id = ?");
$stmt->execute([$message_id, $user_id]);
$message = $stmt->fetch();

if ($message) {
    // Optionally delete associated file
    if ($message['file_path'] && file_exists($message['file_path'])) {
        unlink($message['file_path']);
    }

    // Delete the message from DB
    $del = $db->prepare("DELETE FROM messages WHERE id = ?");
    $del->execute([$message_id]);

    echo "deleted";
} else {
    echo "not_allowed";
}
