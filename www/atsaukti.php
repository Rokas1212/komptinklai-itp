<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

$naudotojoId = $_SESSION['naudotojoId'];
$naudotojasQuery = mysqli_query($mysqli, "SELECT vaidmuo FROM Naudotojai WHERE naudotojo_id = $naudotojoId");
$naudotojas = mysqli_fetch_assoc($naudotojasQuery);

if (!$naudotojas || $naudotojas['vaidmuo'] != 'meistras') {
    $_SESSION['error'] = "Neturite teisės atšaukti šios rezervacijos.";
    header("Location: index.php");
    exit();
}

$rezervacijosId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($rezervacijosId <= 0) {
    $_SESSION['error'] = "Neteisingas rezervacijos identifikatorius.";
    header("Location: meistras.php");
    exit();
}

$rezervacijaQuery = mysqli_query($mysqli, "SELECT meistro_id FROM Rezervacijos WHERE rezervacijos_id = $rezervacijosId");
$rezervacija = mysqli_fetch_assoc($rezervacijaQuery);

if (!$rezervacija) {
    $_SESSION['error'] = "Rezervacija nerasta.";
    header("Location: meistras.php");
    exit();
} elseif ($rezervacija['meistro_id'] != $naudotojoId) {
    $_SESSION['error'] = "Neturite teisės atšaukti šios rezervacijos.";
    header("Location: meistras.php");
    exit();
}

$stmt = mysqli_prepare($mysqli, "DELETE FROM Rezervacijos WHERE rezervacijos_id = ?");
mysqli_stmt_bind_param($stmt, "i", $rezervacijosId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['message'] = "Rezervacija sėkmingai atšaukta.";
} else {
    $_SESSION['error'] = "Nepavyko atšaukti rezervacijos. Bandykite dar kartą.";
}

mysqli_stmt_close($stmt);

header("Location: meistras.php");
exit();