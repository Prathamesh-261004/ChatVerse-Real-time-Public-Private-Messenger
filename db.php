<?php
$dsn = "mysql:host=localhost;dbname=chat_app;charset=utf8mb4";
$db_user = "root"; // change to your MySQL username
$db_pass = "";     // change to your MySQL password

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
