<?php
$host = 'db';
$vardas = 'root';
$slaptazodis = 'stud';
$db = 'autoservisas';

$mysqli = mysqli_connect($host, $vardas, $slaptazodis, $db);

if (!$mysqli) {
    die("Klaida: " . mysqli_connect_error());
}
?>
