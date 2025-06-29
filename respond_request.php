<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$action = $_GET['action'] ?? '';
$request_id = $_GET['id'] ?? 0;

// Fetch the friend request
$stmt = $db->prepare("SELECT * FROM friends WHERE id = ? AND receiver_id = ?");
$stmt->execute([$request_id, $user_id]);
$request = $stmt->fetch();

if (!$request) {
    die("Invalid or unauthorized request.");
}

if ($action === 'accept') {
    $db->prepare("UPDATE friends SET status = 'accepted' WHERE id = ?")->execute([$request_id]);
    // Redirect to chat with the sender
    header("Location: 67_with.php?user_id=" . $request['sender_id']);
    exit;
} elseif ($action === 'reject') {
    $db->prepare("DELETE FROM friends WHERE id = ?")->execute([$request_id]);
    header("Location: friend_requests.php");
    exit;
} else {
    die("Invalid action.");
}
