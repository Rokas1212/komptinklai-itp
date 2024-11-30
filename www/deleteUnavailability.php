<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if the user is authorized
if (!isset($_SESSION['naudotojoId']) || $_SESSION['vaidmuo'] != 'meistras') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$prieinamumoId = intval($data['id']);

$sql = "DELETE FROM Prieinamumas WHERE prieinamumo_id = ? AND meistro_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $prieinamumoId, $_SESSION['naudotojoId']);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
$stmt->close();
?>
