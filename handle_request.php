<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$request_id = $_POST['request_id'] ?? 0;
$action = $_POST['action'] ?? '';

// Validate inputs
if (!in_array($action, ['accept', 'reject']) || !$request_id) {
    die("Invalid input.");
}

// Fetch friend request where current user is the receiver
$stmt = $db->prepare("SELECT * FROM friends WHERE id = ? AND receiver_id = ?");
$stmt->execute([$request_id, $user_id]);
$request = $stmt->fetch();

if (!$request) {
    die("Friend request not found or not authorized.");
}

if ($action === 'accept') {
    $db->prepare("UPDATE friends SET status = 'accepted' WHERE id = ?")->execute([$request_id]);
    header("Location: 67_with.php?user_id=" . $request['sender_id']);
    exit;
} elseif ($action === 'reject') {
    $db->prepare("DELETE FROM friends WHERE id = ?")->execute([$request_id]);
    header("Location: friend_requests.php");
    exit;
}
