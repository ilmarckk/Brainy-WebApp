<?php
session_start();
include 'config.php'; // Assicurati che config.php contenga le informazioni di connessione al database

// Controlla se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    // Se non è loggato, reindirizza alla pagina di login
    header("Location: login.php");
    exit();
}

// Il resto del contenuto della pagina viene mostrato solo se l'utente è loggato

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Ottieni l'user_id dalla sessione
    $message = $conn->real_escape_string($_POST['message']); // Sanifica il messaggio

    // Inserisci il messaggio nel database
    $sql = "INSERT INTO messages (user_id, message) VALUES ('$user_id', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Messaggio inviato con successo!";
        // Redirect alla pagina precedente
        header("Location: support.php");
        exit;
    } else {
        echo "Errore: " . $conn->error;
    }
}
?>
