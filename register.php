<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password_raw = $_POST['password'];

    // Check if username already exists
    $checkUsername = $db->prepare("SELECT * FROM users WHERE username = ?");
    $checkUsername->execute([$username]);

    if ($checkUsername->rowCount() > 0) {
        echo "<p style='color: red; text-align: center;'>❌ Username already taken. Try another.</p>";
        echo "<script>setTimeout(() => window.location.href = 'register.php', 2000);</script>";
        exit;
    }

    // Check if hashed password already exists (not usually required, but included as per request)
    $hashedPassword = password_hash($password_raw, PASSWORD_DEFAULT);
    $checkPassword = $db->prepare("SELECT * FROM users");
    $checkPassword->execute();
    foreach ($checkPassword as $u) {
        if (password_verify($password_raw, $u['password'])) {
            echo "<p style='color: orange; text-align: center;'>⚠️ This password is already in use by another user. Try a different one.</p>";
            echo "<script>setTimeout(() => window.location.href = 'register.php', 2500);</script>";
            exit;
        }
    }

    // Handle profile picture
    $dp = "default.png";
    if (!empty($_FILES['dp']['name'])) {
        $ext = pathinfo($_FILES['dp']['name'], PATHINFO_EXTENSION);
        $dp = 'upload/' . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['dp']['tmp_name'], $dp);
    }

    // Register user
    $stmt = $db->prepare("INSERT INTO users (username, password, dp) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $hashedPassword, $dp]);
        header("Location: login.php?success=1");
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Register | ChatVerse</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(-45deg, #a1c4fd, #c2e9fb, #d4fc79, #96e6a1);
      background-size: 400% 400%;
      animation: bgMove 10s ease infinite;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      background: #fff;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      animation: fadeIn 1s ease;
    }

    h2 {
      margin-bottom: 15px;
      text-align: center;
      color: #333;
      animation: glow 2s ease-in-out infinite alternate;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
    }

    input[type="text"],
    input[type="password"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background: #333;
      color: white;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #000;
    }

    p {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }

    a {
      color: #007bff;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes glow {
      from { text-shadow: 0 0 10px #00c3ff; }
      to { text-shadow: 0 0 25px #3399ff; }
    }

    @media (max-width: 500px) {
      form {
        padding: 20px 15px;
      }
    }
  </style>
</head>
<body>
  <form method="POST" enctype="multipart/form-data">
    <h2>📝 Register on ChatVerse</h2>
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Profile Picture (optional):</label>
    <input type="file" name="dp" accept="image/*">

    <button type="submit">Register</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </form>
</body>
</html>
