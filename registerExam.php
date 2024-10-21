<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Devi essere loggato per registrarti']);
    exit;
}

$userId = $_SESSION['user_id'];
$examId = $_POST['exam_id'];

// Controlla se l'ID dell'esame è valido
if (empty($examId)) {
    echo json_encode(['status' => 'error', 'message' => 'ID esame non valido']);
    exit;
}

// Verifica se l'utente è già iscritto a un altro esame
$sql = "SELECT e.id AS exam_id, e.nome, e.orario, e.posti_disponibili 
        FROM esami e
        JOIN registrazioni r ON e.id = r.esame_id
        WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$alreadyRegistered = false;
$registeredExamId = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['exam_id'] == $examId) {
            $alreadyRegistered = true;
            break;
        }
    }
    
    if ($alreadyRegistered) {
        echo json_encode(['status' => 'error', 'message' => 'Sei già iscritto/a a questo esame']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sei già iscritto/a ad un altro esame']);
        exit;
    }
}

// Verifica se ci sono posti disponibili per l'esame selezionato
$sql = "SELECT posti_disponibili FROM esami WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $examId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Esame non trovato']);
    exit;
}

$row = $result->fetch_assoc();

if ($row['posti_disponibili'] <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Nessun posto disponibile']);
    exit;
}

// Registra l'utente e decrementa i posti disponibili
$conn->begin_transaction();

try {
    // Inserisce la registrazione
    $sql = "INSERT INTO registrazioni (user_id, esame_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $userId, $examId);
    $stmt->execute();

    // Decrementa i posti disponibili
    $sql = "UPDATE esami SET posti_disponibili = posti_disponibili - 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $examId);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Registrazione avvenuta con successo']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Errore durante la registrazione']);
}
?>
