<?php
session_start();

// Controlla se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Se non è loggato, reindirizza alla pagina di login
    header("Location: login.php");
    exit();
}

// Il resto del contenuto della pagina viene mostrato solo se l'utente è loggato
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAINY - Supporto</title>
    <link rel="stylesheet" href="profileStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="icon_512.png" type="image/png">
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
        <a href="homePage.php"><img src="arrow.png" alt="backArrow" class="back-arrow"></a>
        <h1>BRAINY</h1>
    </header>
    <main>
        <div class="user-info">
            <p><?php echo $_SESSION['username']; ?></p>
        </div>
        
        <div class="support-options">
            <div class="message-form">
                <form id="support-form" action="sendMessage.php" method="POST">
                    <label for="message">Hai bisogno di aiuto o hai un feedback?</label>
                    <textarea id="message" name="message" required></textarea>
                    <button type="submit">Invia</button>
                </form>
            </div>
        </div>
        <div class="forgot-password">
            <a href="logout.php">Logout</a>
        </div>

        <div class="credits">
            <p>Questa WebApp è stata realizzata da <br>Marco Francavilla <br>Lugo - RA <br>4BSA</p>
        </div>
    </main>

    <script src="support.js"></script>
</body>
</html>
