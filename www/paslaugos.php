<?php
include 'db.php';
session_start();

if (!isset($_SESSION['naudotojoId'])) {
    header('Location: prisijungimas.php');
    exit();
}
include 'header.php';

?>

<div class="container">
    <h1>Pasirinkite meistrą</h1>

    <form id="mechanic-form" action="rezervacija.php" method="get">
        <input type="hidden" name="meistro_id" id="pasirinktas-meistro-id" required>
        <?php
            $result = mysqli_query($mysqli, "SELECT naudotojo_id, vardas FROM Naudotojai WHERE vaidmuo = 'meistras'");

            while ($row = mysqli_fetch_assoc($result)) {
                $meistroId = $row['naudotojo_id'];
                $meistras = htmlspecialchars($row['vardas']);
                $hardcodedRating = 4.5;

                echo "<div id='meistro-profilis-$meistroId' class='meistro-profilis mt-3 p-3 border rounded' onclick='pasirinktiMeistra($meistroId)'>";
                echo "<img src='nuotraukos/mekanik.jpg' alt='Meistro nuotrauka' style='width:150px; height:150px; border-radius:50%;'>";
                echo "<h3>$meistras</h3>";
                echo "<p>Įvertinimas: {$hardcodedRating} / 5</p>";
                echo "</div>";
            }
        ?>
    </form>
</div>

<script>
function pasirinktiMeistra(meistroId) {
    document.getElementById('pasirinktas-meistro-id').value = meistroId;

    document.getElementById('mechanic-form').submit();
}
</script>

<style>
.meistro-profilis {
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.meistro-profilis:hover {
    background-color: #f1f1f1;
}
</style>

<?php include 'footer.php'; ?>