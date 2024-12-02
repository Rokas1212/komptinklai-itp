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
    <h1 class="mb-4">Pasirinkite paslaugą</h1>
    <form id="service-form" action="rezervacija.php" method="get">
        <input type="hidden" name="paslaugos-id" id="pasirinktas-paslaugos-id" required>
        <div class="row">
            <?php
            $result = mysqli_query($mysqli, "SELECT * FROM Paslaugos");
            while ($row = mysqli_fetch_assoc($result)) {

                $paslaugosId = $row['paslaugos_id'];
                $aprasymas = htmlspecialchars($row['aprasymas']) ?? "Aprašymas nepateiktas.";
                $nuotrauka = (empty($row['nuotrauka']) ? 'nuotraukos/paslauga.png' : htmlspecialchars($row['nuotrauka'], ENT_QUOTES, 'UTF-8'));
                $paslauga = htmlspecialchars($row['paslaugos_pavadinimas']);
                $kaina = htmlspecialchars($row['kaina']);
                

                echo "<div class='col-lg-4 col-md-6 mb-4'>";
                echo "<div id='meistro-profilis-$paslaugosId' class='card h-100 border-0 shadow' onclick='pasirinktiPaslauga($paslaugosId)'>";
                echo "<img src='$nuotrauka' class='card-img-top img-fluid' alt='Paslaugos nuotrauka'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title text-primary'>$paslauga</h5>";
                echo "<p class='card-text text-muted'>$aprasymas</p>";
                echo "<div class='d-flex justify-content-between align-items-center'>";
                echo "<span class='text-muted'>€ {$kaina}</span>";
                echo "</div>";
                echo "</div>"; // card-body
                echo "</div>"; // card
                echo "</div>"; // col
            }
            ?>
        </div>
    </form>
</div>


<script>
function pasirinktiPaslauga(paslaugosId) {
    document.getElementById('pasirinktas-paslaugos-id').value = paslaugosId;

    document.getElementById('service-form').submit();
}
</script>

<style>
.card {
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.card-img-top {
    height: 300px;
    object-fit: contain;
    background-color: #f8f9fa; 
}
</style>

<?php include 'footer.php'; ?>