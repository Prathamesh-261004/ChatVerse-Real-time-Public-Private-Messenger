<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) exit;
$user_id = $_SESSION['user']['id'];

$message = $_POST['message'] ?? '';
$receiver_id = $_POST['receiver_id'] ?: null;
$file_path = null;
$file_type = null;

// Handle file
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $upload_dir = "upload/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);
    
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $file_type = mime_content_type($_FILES['file']['tmp_name']);
    $filename = uniqid() . "." . $ext;
    $file_path = $upload_dir . $filename;
    move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
}

$stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path, file_type) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $receiver_id, $message, $file_path, $file_type]);
