<?php
session_start();
include 'config.php'; // Assicurati che config.php contenga le informazioni di connessione al database

$error = '';

// Controlla se esistono i cookie per il login automatico
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    // Prepara e esegui la query per trovare l'utente tramite remember_token
    $stmt = $conn->prepare("SELECT id, username, name FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    // Se l'utente esiste con il token fornito
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $username, $name);
        $stmt->fetch();

        // Ricrea la sessione per l'utente autenticato
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $name;

        // Aggiorna il campo last_login con la data e ora attuale
        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $userId);
        $updateStmt->execute();
        $updateStmt->close();

        // Reindirizza alla home page
        header("Location: homePage.php");
        exit();
    }
    $stmt->close();
}

// Verifica se il modulo di login è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottieni e sanitizza i dati del modulo
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepara e esegui la query per recuperare l'utente
        $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Verifica se l'utente esiste
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $hashedPassword, $name);
            $stmt->fetch();

            // Verifica la password
            if (password_verify($password, $hashedPassword)) {
                // Password corretta, crea una sessione per l'utente
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['name'] = $name;

                // Aggiorna il campo last_login con la data e ora attuale
                $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $userId);
                $updateStmt->execute();
                $updateStmt->close();

                // Controlla se l'utente ha selezionato "Ricordami"
                if (isset($_POST['remember_me'])) {
                    // Genera un token sicuro per il remember_token
                    $rememberToken = bin2hex(random_bytes(16));

                    // Salva il token nel database
                    $tokenStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $tokenStmt->bind_param("si", $rememberToken, $userId);
                    $tokenStmt->execute();
                    $tokenStmt->close();

                    // Imposta il cookie per ricordare l'utente (valido per 30 giorni)
                    setcookie('remember_token', $rememberToken, time() + (86400 * 30), "/");
                }

                // Reindirizza alla home page
                header("Location: homePage.php");
                exit();
            } else {
                $error = "Password errata.";
            }
        } else {
            $error = "Nome utente non trovato.";
        }

        $stmt->close();
    } else {
        $error = "Per favore, riempi tutti i campi.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brainy</title>
    <link rel="stylesheet" href="loginStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="icon" href="icon_512.png" type="image/png">
    <link rel="manifest" href="manifest.json">
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
    <div class="container">
        <h1>BRAINY</h1>
        <div class="login-box">
            <form method="POST" action="">
                <label for="username">Nome utente</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label>
                    <input type="checkbox" name="remember_me"> Ricordami
                </label>
                
                <button type="submit">ACCEDI</button>
            </form>
            <?php if ($error): ?>
                <p class='error'><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
