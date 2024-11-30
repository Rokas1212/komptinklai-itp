<?php
session_start();
include 'db.php';

if($_SESSION['vaidmuo'] != 'meistras') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['meistro_id']) && intval($_POST['meistro_id']) === $_SESSION['naudotojoId']) {
    $meistro_id = intval($_POST['meistro_id']);
    $selected_services = isset($_POST['services']) ? $_POST['services'] : [];

    $delete_query = "DELETE FROM MeistrasPaslaugos WHERE meistro_id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $meistro_id);
    $stmt->execute();
    $stmt->close();

    $insert_query = "INSERT INTO MeistrasPaslaugos (meistro_id, paslaugos_id) VALUES (?, ?)";
    $stmt = $mysqli->prepare($insert_query);
    foreach ($selected_services as $service_id) {
        $stmt->bind_param("ii", $meistro_id, $service_id);
        $stmt->execute();
    }
    $stmt->close();

    header("Location: meistras.php?success=1");
    exit();
}
?>
