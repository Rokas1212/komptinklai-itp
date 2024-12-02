<?php
session_start();
include '../db.php';
include '../mechanicWeek.php'; // Ensure this includes your helper functions.

header('Content-Type: application/json');

// Check for required parameters.
if (!isset($_GET['meistro_id']) || !isset($_GET['date'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit();
}

$meistroId = intval($_GET['meistro_id']);
$date = $_GET['date'];

// Fetch all time slots for the day using `getTimeSlots`.
$timeSlots = getTimeSlots('09:00', '18:00', '+60 minutes');

// Fetch unavailable times (reservations and explicitly blocked times).
$unavailable = [];

// Fetch reservations for the specific mechanic on the given date.
$query = "SELECT rezervacijos_laikas FROM Rezervacijos WHERE meistro_id = ? AND rezervacijos_data = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('is', $meistroId, $date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $unavailable[] = $row['rezervacijos_laikas'];
}
$stmt->close();

// Fetch explicitly unavailable times for the mechanic.
$query = "SELECT laikas FROM Prieinamumas WHERE meistro_id = ? AND data = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('is', $meistroId, $date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $unavailable[] = $row['laikas'];
}
$stmt->close();

// Return available and unavailable times.
echo json_encode([
    'success' => true,
    'times' => $timeSlots,
    'unavailable' => array_unique($unavailable),
]);
