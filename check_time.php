<?php
date_default_timezone_set('UTC'); // Imposta il fuso orario UTC
$targetTime = '2024-08-23 14:00:00';
$targetDate = new DateTime($targetTime);
$currentDate = new DateTime();

if ($currentDate >= $targetDate) {
    echo json_encode(['status' => 'enabled']);
} else {
    $remainingTime = $targetDate->getTimestamp() - $currentDate->getTimestamp();
    echo json_encode(['status' => 'disabled', 'remaining' => $remainingTime]);
}
?>
