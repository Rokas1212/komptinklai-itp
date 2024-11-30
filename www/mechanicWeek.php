<?php

function getWeekdays($startOfWeek = 'monday this week') {
    $weekdays = [];
    $start = strtotime($startOfWeek);
    for ($i = 0; $i < 7; $i++) {
        $weekdays[] = date('Y-m-d', strtotime("+$i day", $start));
    }
    return $weekdays;
}

function getTimeSlots($startTime = '09:00', $endTime = '18:00', $interval = '+60 minutes') {
    $timeSlots = [];
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    while ($start <= $end) {
        $timeSlots[] = date('H:i:s', $start);
        $start = strtotime($interval, $start);
    }
    return $timeSlots;
}

function getReservationsForWeek($mysqli, $mechanicId, $startOfWeek, $endOfWeek) {
    $reservations = [];
    $query = "
        SELECT rezervacijos_data, rezervacijos_laikas, paslaugos_id 
        FROM Rezervacijos 
        WHERE meistro_id = ? AND rezervacijos_data BETWEEN ? AND ?
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iss', $mechanicId, $startOfWeek, $endOfWeek);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $reservations[$row['rezervacijos_data']][$row['rezervacijos_laikas']] = $row['paslaugos_id'];
    }
    $stmt->close();
    return $reservations;
}

function getUnavailableTimeSlotsWeek($mysqli, $mechanicId, $startOfWeek, $endOfWeek) {
    $unavailableTimeSlots = [];
    $query = "
        SELECT laikas, data 
        FROM Prieinamumas
        WHERE meistro_id = ? AND data BETWEEN ? AND ?
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iss', $mechanicId, $startOfWeek, $endOfWeek);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $unavailableTimeSlots[$row['data']][] = $row['laikas'];
    }
    $stmt->close();
    return $unavailableTimeSlots;
}

function getWorkingHours($start, $end){
    $workingHours = [];
    while ($start <= $end) {
        $workingHours[] = date('H:i:s', $start);
        $start = strtotime('+60 minutes', $start);
    }
    return $workingHours;
}