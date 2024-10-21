<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$currentTime = date('Y-m-d H:i:s'); // Ottieni l'orario attuale

$sql = "SELECT id, nome, orario, posti_disponibili, orario_inizio_iscrizione
        FROM esami";
$result = $conn->query($sql);

$exams = [];

while ($row = $result->fetch_assoc()) {
    $exams[] = [
        'id' => $row['id'],
        'nome' => $row['nome'],
        'orario' => $row['orario'],
        'posti_disponibili' => $row['posti_disponibili'],
        'is_open' => ($row['orario_inizio_iscrizione'] && $currentTime >= $row['orario_inizio_iscrizione'])
    ];
}

echo json_encode($exams);
?>
