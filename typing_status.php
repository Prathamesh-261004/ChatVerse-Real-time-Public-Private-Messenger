<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) exit;

$files = glob("typing_*.txt");
$status = [];

foreach ($files as $file) {
    $uid = str_replace(['typing_', '.txt'], '', basename($file));
    if ($uid == $_SESSION['user']['id']) continue;

    $time = (int)file_get_contents($file);
    if (time() - $time < 5) {
        $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $u = $stmt->fetch();
        if ($u) $status[] = $u['username'] . " is typing...";
    }
}

echo implode("<br>", $status);
