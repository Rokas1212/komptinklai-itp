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
    <h1 class="mb-4">Pasirinkite meistrą</h1>
    <form id="mechanic-form" action="rezervacija.php" method="get">
        <input type="hidden" name="meistro_id" id="pasirinktas-meistro-id" required>
        <div class="row">
            <?php
            $result = mysqli_query($mysqli, "SELECT naudotojo_id, aprasymas, vardas, nuotrauka FROM Naudotojai WHERE vaidmuo = 'meistras'");
            while ($row = mysqli_fetch_assoc($result)) {
                $meistroId = $row['naudotojo_id'];
                $aprasymas = htmlspecialchars($row['aprasymas']) ?? "Aprašymas nepateiktas.";

                $stmt = $mysqli->prepare("SELECT ROUND(AVG(rating), 2) as vidurkis, COUNT(rating) as kiekis FROM Ratings WHERE meistro_id = ?");
                $stmt->bind_param("i", $meistroId);
                $stmt->execute();
                $ratingQuery = $stmt->get_result();
                $ratingResult = $ratingQuery->fetch_assoc();
                
                $rating = $ratingResult['vidurkis'] ?? 0;
                $kiekis = $ratingResult['kiekis'] ?? 0;;
                
                
                $nuotrauka = (htmlspecialchars($row['nuotrauka']) == "" ? 'nuotraukos/mekanik.jpg' : htmlspecialchars($row['nuotrauka']));
                $meistras = htmlspecialchars($row['vardas']);

                echo "<div class='col-lg-4 col-md-6 mb-4'>";
                echo "<div id='meistro-profilis-$meistroId' class='card h-100 border-0 shadow' onclick='pasirinktiMeistra($meistroId)'>";
                echo "<img src='$nuotrauka' class='card-img-top img-fluid' alt='Meistro nuotrauka'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title text-primary'>$meistras</h5>";
                echo "<p class='card-text text-muted'>$aprasymas</p>";
                echo "<div class='d-flex justify-content-between align-items-center'>";
                echo "<span class='text-muted'>Įvertinimas: {$rating} / 5</span>";
                echo "<span class='text-muted'>Įvertinimų kiekis: {$kiekis}</span>";
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
function pasirinktiMeistra(meistroId) {
    document.getElementById('pasirinktas-meistro-id').value = meistroId;

    document.getElementById('mechanic-form').submit();
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