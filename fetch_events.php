<?php
session_start();
require_once 'config.php'; // Assicurati che il percorso sia corretto

// Verifica che la sessione contenga un user_id
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Connessione al database è già stabilita tramite config.php
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$date) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'No date provided']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepara la query per ottenere gli eventi
$query = "
    SELECT 'class' AS type, date, event AS title FROM class_appointments WHERE date = ?
    UNION
    SELECT 'announcement' AS type, date, announcement AS title FROM announcements WHERE date = ?
    UNION
    SELECT 'announcement' AS type, date, announcement AS title FROM announcements_table WHERE date = ?
    UNION
    SELECT 'personal' AS type, date, event AS title FROM personal_appointments WHERE date = ? AND user_id = ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare query']);
    exit;
}

// Usa il binding dei parametri per prevenire SQL Injection
$stmt->bind_param('sssss', $date, $date, $date, $date, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events);

$stmt->close();
$conn->close();
?>
