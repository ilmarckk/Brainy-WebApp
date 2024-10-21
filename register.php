<?php
include 'config.php'; // Include il file di configurazione del database

$error = '';
$success = '';

// Verifica se il modulo è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottieni i dati del modulo e proteggili da SQL injection
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);

    // Validazione dei dati
    if (empty($username) || empty($password) || empty($name)) {
        $error = "Tutti i campi sono obbligatori.";
    } else {
        // Verifica se l'utente esiste già
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Nome utente già esistente.";
        } else {
            // Prepara e esegui la query per inserire il nuovo utente
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $name);

            if ($stmt->execute()) {
                $success = "Registrazione completata. Puoi ora <a href='login.php'>accedere</a>.";
            } else {
                $error = "Errore nella registrazione. Riprova.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Brainy</title>
    <link rel="stylesheet" href="loginStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
    <div class="container">
        <h1>BRAINY </h1>
        <div class="login-box">
            <form method="POST" action="">
                <label for="username">Nome utente</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                
                <label for="name">Nome completo</label>
                <input type="text" id="name" name="name" required>
                
                <button type="submit">REGISTRATI</button>
            </form>
        </div>
        <div class="links">
            <?php
            if ($success) {
                echo "<p style='color: green;'>$success</p>";
            } elseif ($error) {
                echo "<p style='color: red;'>$error</p>";
            }
            ?>
            <a href="index.php">Torna al login</a>
        </div>
    </div>
</body>
</html>
