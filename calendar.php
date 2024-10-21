<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAINY - Calendario</title>
    <link rel="stylesheet" href="calendarStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="icon_512.png" type="image/png">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QXKXBSM8B8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QXKXBSM8B8');
</script>
</head>
<body>
    <header>
        <h1>BRAINY</h1>
        <a href="profile.php" class="profile-icon"><img src="profile_icon.png" alt="Profile Icon"></a>
    </header>

    <main>
        <h2>Calendario</h2>
        <div id="calendar-container">
            <div id="selected-date">
                <h3 id="current-date"></h3>
                <div id="events">
                    <h4 class="eventi">Eventi:</h4>
                    <div id="event-list"></div>
                </div>
            </div>
            <div id="year-navigation">
                <button id="prev-year">&lt;</button>
                <span id="current-year"></span>
                <button id="next-year">&gt;</button>
            </div>
            <div id="month-navigation">
                <button id="prev-month">&lt;</button>
                <span id="current-month"></span>
                <button id="next-month">&gt;</button>
            </div>
            <div id="calendar"></div>
        </div>
    </main>

    <nav class="navbar">
        <a href="homePage.php">
            <img src="home.png" alt="Home" class="navbar-icon">
            <span>Home</span>
        </a>
        <a href="calendar.php">
            <img src="calendar.png" alt="Calendario" class="navbar-icon">
            <span style="font-size: 15px;">Calendario</span>
        </a>
        <a href="registrationExam.html">
            <img src="board.png" alt="Bacheca" class="navbar-icon">
            <span>Prenota</span>
        </a>
    </nav>

    <script src="calendarScript.js"></script>
</body>
</html>
