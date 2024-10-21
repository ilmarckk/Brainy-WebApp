<?php
session_start();
include 'config.php'; // Assicurati che config.php contenga le informazioni di connessione al database

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Reindirizza al login se non è loggato
    exit();
}

$userId = $_SESSION['user_id']; // Ottieni l'ID utente dalla sessione
$userName = $_SESSION['name']; // Ottieni il nome utente dalla sessione

// Query per recuperare gli eventi in evidenza (impegni più vicini)
$evidenzaQuery = "
    SELECT date, event
    FROM (
        SELECT date, event
        FROM class_appointments
        WHERE date >= CURDATE()
        UNION
        SELECT date, event
        FROM personal_appointments
        WHERE date >= CURDATE() AND user_id = ?
        UNION
        SELECT date, announcement AS event
        FROM announcements
        WHERE date >= CURDATE()
    ) AS all_events
    ORDER BY date ASC
    LIMIT 4
";

$stmt = $conn->prepare($evidenzaQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$evidenzaResult = $stmt->get_result();

// Query per statistiche degli impegni personali
$statsQuery = "SELECT type, COUNT(*) as count FROM personal_appointments WHERE user_id = ? GROUP BY type";
$stmt = $conn->prepare($statsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$statsResult = $stmt->get_result();

// Recupera solo gli impegni personali dell'utente loggato
$personalAppointmentsQuery = "SELECT date, event FROM personal_appointments WHERE user_id = ? ORDER BY date ASC";
$stmt = $conn->prepare($personalAppointmentsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$personalAppointmentsResult = $stmt->get_result();

// Recupera gli impegni della classe
$classAppointmentsQuery = "SELECT date, event FROM class_appointments ORDER BY date ASC";
$classAppointmentsResult = $conn->query($classAppointmentsQuery);

// Recupera gli avvisi
$avvisiQuery = "SELECT date, announcement FROM announcements ORDER BY date DESC";
$avvisiResult = $conn->query($avvisiQuery);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRAINY</title>
    <link rel="stylesheet" href="homeStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="icon_512.png" type="image/png">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Brainy">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
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
        <h2>Ciao, <?php echo htmlspecialchars($userName); ?></h2>
        <div class="info-container">
            <div class="evidenza">
                <h3>In Evidenza:</h3>
                <?php
                if ($evidenzaResult->num_rows > 0) {
                    while ($row = $evidenzaResult->fetch_assoc()) {
                        echo "<div class='evento'>";
                        echo "<span class='data'>" . date('D d', strtotime($row['date'])) . "</span>";
                        echo "<span class='nome-evento'>" . htmlspecialchars($row['event']) . "</span>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>

        <section class="impegni">
            <h3>Impegni della classe</h3>
            <div class="carousel">
                <?php
                if ($classAppointmentsResult->num_rows > 0) {
                    while ($row = $classAppointmentsResult->fetch_assoc()) {
                        echo "<div class='item'>";
                        echo "<p>" . date('D d M', strtotime($row['date'])) . ":</p>";
                        echo "<p>" . htmlspecialchars($row['event']) . "</p>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </section>

        <section class="personal-impegni">
            <h3>I tuoi impegni personali</h3>
            <div class="carousel">
                <?php
                if ($personalAppointmentsResult->num_rows > 0) {
                    while ($row = $personalAppointmentsResult->fetch_assoc()) {
                        echo "<div class='item'>";
                        echo "<p>" . date('D d M', strtotime($row['date'])) . ":</p>";
                        echo "<p>" . htmlspecialchars($row['event']) . "</p>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </section>

        <section class="avvisi">
            <h3>Avvisi</h3>
            <div class="carousel">
                <?php
                if ($avvisiResult->num_rows > 0) {
                    while ($row = $avvisiResult->fetch_assoc()) {
                        echo "<div class='item'>";
                        echo "<p>" . date('D d M', strtotime($row['date'])) . ":</p>";
                        echo "<p>" . htmlspecialchars($row['announcement']) . "</p>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </section>
    </main>

    <nav class="navbar">
        <a href="homePage.php" class="active">
            <img src="home.png" alt="Home" class="navbar-icon">
            <span style="font-size: 15px;">Home</span>
        </a>
        <a href="calendar.php">
            <img src="calendar.png" alt="Calendario" class="navbar-icon">
            <span>Calendario</span>
        </a>
        <a href="registrationExam.html">
            <img src="board.png" alt="Bacheca" class="navbar-icon">
            <span>Prenota</span>
        </a>
    </nav>
    <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('service-worker.js')
          .then(function(registration) {
            console.log('Service Worker registrato con successo:', registration.scope);
          })
          .catch(function(error) {
            console.log('Service Worker non registrato:', error);
          });
      });
    }
  </script>
</body>
</html>
