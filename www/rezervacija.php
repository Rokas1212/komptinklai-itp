<?php
// rezervacija.php - nauja rezervacija vizitui

include 'db.php';

function sukurtiRezervacija($kliento_id, $meistro_id, $paslaugos_id, $rezervacijos_data, $rezervacijos_laikas) {
    global $mysqli;
    $stmt = mysqli_prepare($mysqli, "INSERT INTO Rezervacijos (kliento_id, meistro_id, paslaugos_id, rezervacijos_data, rezervacijos_laikas) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiiss", $kliento_id, $meistro_id, $paslaugos_id, $rezervacijos_data, $rezervacijos_laikas);
        mysqli_stmt_execute($stmt);
        $insertedId = mysqli_insert_id($mysqli);
        mysqli_stmt_close($stmt);
        return $insertedId;
}

// Example usage
$appointmentId = sukurtiRezervacija(1, 2, 1, '2024-11-01', '10:00:00');
echo "Appointment created with ID: " . $appointmentId;
?>

