<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) exit;

$stmt = $db->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
$stmt->execute([$_SESSION['user']['id']]);
