<?php
session_start();
include 'config.php'; // Assicurati che config.php contenga le informazioni di connessione al database

// Verifica se l'utente è loggato
if (isset($_SESSION['user_id'])) {
    // Recupera l'ID utente
    $userId = $_SESSION['user_id'];

    // Rimuovi il token di ricordo dal database
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();

    // Rimuovi il cookie di remember_token
    setcookie('remember_token', '', time() - 3600, '/'); // Imposta la data di scadenza al passato

    // Distruggi la sessione
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Reindirizza alla pagina di login
    header("Location: index.php");
    exit();
} else {
    // Se non c'è una sessione attiva, reindirizza alla pagina di login
    header("Location: index.php");
    exit();
}
?>
