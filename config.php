<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_bbrainy";

// Crea la connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
