
<?php
include 'db.php';
session_start();

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

$meistro_id = isset($_GET['meistro_id']) ? $_GET['meistro_id'] : null;

if ($meistro_id) {
    $stmt = mysqli_prepare($mysqli, "SELECT vardas FROM Naudotojai WHERE naudotojo_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $meistro_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($mechanic = mysqli_fetch_assoc($result)) {
        $profile_picture = "nuotraukos/mekanik.jpg";
        $rating = "★★★★☆ (4.5)";

        echo "<img src='$profile_picture' alt='Meistro nuotrauka' style='width:150px; height:150px; border-radius:50%;'>";
        echo "<h2>{$mechanic['vardas']}</h2>";
        echo "<p><strong>Įvertinimas:</strong> $rating</p>";
    } else {
        echo "<p>Meistras nerastas.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>Meistras nepasirinktas.</p>";
    echo "<p>Grįžkite į <a href='paslaugos.php'>meistro pasirinkimo</a> puslapį.</p>";
    exit();
}
?>