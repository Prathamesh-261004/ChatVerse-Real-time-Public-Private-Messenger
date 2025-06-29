<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) exit;
$user_id = $_SESSION['user']['id'];
$scope = $_GET['scope'] ?? 'public';
$receiver_id = $_GET['receiver_id'] ?? null;

$params = [];
$sql = "
SELECT m.*, u.username FROM messages m
JOIN users u ON u.id = m.sender_id
";

if ($scope === "private" && $receiver_id) {
    $sql .= " WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
    $params = [$user_id, $receiver_id, $receiver_id, $user_id];
} else {
    $sql .= " WHERE m.receiver_id IS NULL"; // public chat
}

$sql .= " ORDER BY m.id ASC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

$lastSentByMe = null;

foreach ($messages as $msg) {
    $isMine = $msg['sender_id'] == $user_id;
    $isPrivate = $msg['receiver_id'] !== null;
    $class = $isMine ? "you" : ($isPrivate ? "private-other" : "public-other");

    // Media handling
    $file_html = "";
    if ($msg['file_path']) {
        $ext = strtolower(pathinfo($msg['file_path'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $file_html = "<br><img src='{$msg['file_path']}' style='max-width:200px;border-radius:8px;'>";
        } elseif ($ext === 'mp4') {
            $file_html = "<br><video controls src='{$msg['file_path']}' style='max-width:200px;'></video>";
        } elseif (in_array($ext, ['mp3', 'wav'])) {
            $file_html = "<br><audio controls src='{$msg['file_path']}'></audio>";
        } else {
            $file_html = "<br><a href='{$msg['file_path']}' target='_blank'>📄 Download File</a>";
        }
    }

    // Mark as seen if this is a private message sent to current user
    if ($isPrivate && $msg['receiver_id'] == $user_id && !$msg['seen']) {
        $upd = $db->prepare("UPDATE messages SET seen = 1, seen_at = NOW() WHERE id = ?");
        $upd->execute([$msg['id']]);
    }

    // Track last message I sent
    if ($isMine && $isPrivate) {
        $lastSentByMe = $msg;
    }

    // Output message
    echo "<div class='msg $class' data-msg-id='{$msg['id']}'>";
    echo "<div class='sender'>" . htmlspecialchars($msg['username']) . ":</div>";
    echo nl2br(htmlspecialchars($msg['message'])) . $file_html;

    // Fix: Use created_at instead of timestamp
    $time = isset($msg['created_at']) ? date("h:i A", strtotime($msg['created_at'])) : "";
    echo "<br><small style='font-size:11px;color:#aaa;'>$time</small>";

if ($isMine && $isPrivate) {
    echo "<span class='delete-btn' onclick='deleteMsg({$msg['id']})'>🗑️</span>";
}



// Show delivery/seen status for last private message I sent
if ($scope === "private" && $lastSentByMe) {
    echo "<div class='status' style='text-align:right; font-size:12px; color:#ccc; margin-top:5px;'>";
    if ($lastSentByMe['seen']) {
        $seenAt = date("h:i A", strtotime($lastSentByMe['seen_at']));
        echo "✅ Seen at $seenAt";
    } else {
        echo "✅ Delivered";
    }
    echo "</div>";
}
}
