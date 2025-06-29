<?php
session_start();
$redirect_to = isset($_SESSION['user']) ? 'public_67.php' : 'login.php';
header("refresh:10;url=$redirect_to");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to ChatVerse</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
      background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #fbc2eb, #a6c1ee);
      background-size: 400% 400%;
      animation: bgMove 12s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      height: 100vh;
      color: #222;
      transition: 0.4s;
      position: relative;
    }
    body.dark {
      background: #0d0d0d;
      color: #eee;
    }

    h1 {
      font-size: 4em;
      text-shadow: 0 0 15px #ff6600;
      animation: glow 2s ease-in-out infinite alternate;
      margin-bottom: 10px;
      text-align: center;
    }

    h1::after {
      content: 'Let\'s Chat';
      display: block;
      font-size: 0.3em;
      opacity: 0.7;
      margin-top: 5px;
      animation: fadeIn 3s ease-in-out;
    }

    .typing {
      font-size: 1.2em;
      white-space: nowrap;
      overflow: hidden;
      width: 24ch;
      border-right: 3px solid #222;
      animation: typing 3s steps(24), blink 0.7s step-end infinite;
    }

    .loader {
      margin: 30px auto 10px;
      width: 55px;
      height: 55px;
      border: 6px solid #eee;
      border-top: 6px solid #ff7300;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    .controls {
      margin-top: 20px;
      display: flex;
      gap: 10px;
    }

    button {
      padding: 10px 18px;
      border-radius: 25px;
      border: none;
      font-size: 14px;
      background: #222;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #000;
    }

    @keyframes bgMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    @keyframes glow {
      from { text-shadow: 0 0 10px #ffa500; }
      to { text-shadow: 0 0 25px #ff5500; }
    }
    @keyframes typing {
      from { width: 0; }
      to { width: 24ch; }
    }
    @keyframes blink {
      50% { border-color: transparent; }
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Bubble Effect */
    .bubble {
      position: absolute;
      bottom: -50px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      animation: float 10s linear infinite;
    }

    .bubble:nth-child(1) { width: 40px; height: 40px; left: 10%; animation-delay: 0s; }
    .bubble:nth-child(2) { width: 25px; height: 25px; left: 30%; animation-delay: 2s; }
    .bubble:nth-child(3) { width: 60px; height: 60px; left: 50%; animation-delay: 4s; }
    .bubble:nth-child(4) { width: 35px; height: 35px; left: 70%; animation-delay: 1s; }
    .bubble:nth-child(5) { width: 20px; height: 20px; left: 90%; animation-delay: 3s; }

    @keyframes float {
      0% { transform: translateY(0); opacity: 0.5; }
      100% { transform: translateY(-120vh); opacity: 0; }
    }

    @media(max-width: 600px) {
      h1 { font-size: 2.2em; }
      .typing { font-size: 1em; width: 100%; }
      button { font-size: 12px; padding: 8px 12px; }
    }
  </style>
</head>
<body>
  <h1>💬 ChatVerse</h1>
  <div class="typing">Connecting you securely...</div>
  <div class="loader"></div>
  <div class="controls">
    <button onclick="toggleDark()">🌓 Dark Mode</button>
    <button onclick="toggleMusic()">🎵 Music</button>
  </div>

  <!-- Floating bubbles -->
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>
  <div class="bubble"></div>

 <!-- Background music -->
<audio id="bg-music" autoplay loop>
  <source src="https://www.bensound.com/bensound-music/bensound-sunny.mp3" type="audio/mp3">
</audio>

<script>
  let music = document.getElementById("bg-music");
  let playing = true;

  // Attempt autoplay on page load
  window.addEventListener('DOMContentLoaded', () => {
    const playPromise = music.play();
    if (playPromise !== undefined) {
      playPromise
        .then(() => console.log("Music autoplayed"))
        .catch(err => {
          console.warn("Autoplay blocked, will require user interaction");
        });
    }
  });

  function toggleMusic() {
    if (playing) music.pause();
    else music.play();
    playing = !playing;
  }
</script>


  <script>
    let isDark = false;
    function toggleDark() {
      document.body.classList.toggle("dark");
      isDark = !isDark;
    }

    let music = document.getElementById("bg-music");
    let playing = true;
    function toggleMusic() {
      if (playing) music.pause();
      else music.play();
      playing = !playing;
    }
  </script>
</body>
</html>
