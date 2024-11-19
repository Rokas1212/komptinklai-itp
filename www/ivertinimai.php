<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meistro_id = $_POST['meistro_id'];
    $kliento_id = $_SESSION['naudotojoId'];
    $rating = $_POST['rating'];

    $sql = "SELECT * FROM Ratings WHERE meistro_id = ? AND kliento_id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => $mysqli->error]);
        exit();
    }
    $stmt->bind_param('ii', $meistro_id, $kliento_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        updateRating($meistro_id, $kliento_id, $rating);
        echo json_encode(['success' => true, 'message' => 'Rating updated']);
    } else {
        $stmt->close();
        insertRating($meistro_id, $kliento_id, $rating);
        echo json_encode(['success' => true, 'message' => 'Rating saved']);
    }
    exit();
}

function insertRating($meistro_id, $kliento_id, $rating) {
    global $mysqli;
    $sql = "INSERT INTO Ratings (meistro_id, kliento_id, rating) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('iii', $meistro_id, $kliento_id, $rating);
    $stmt->execute();
    $stmt->close();
}

function updateRating($meistro_id, $kliento_id, $rating) {
    global $mysqli;
    $sql = "UPDATE Ratings SET rating = ? WHERE meistro_id = ? AND kliento_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('iii', $rating, $meistro_id, $kliento_id);
    $stmt->execute();
    $stmt->close();
}
