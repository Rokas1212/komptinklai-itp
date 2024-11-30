<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

if (!isset($_GET['paslaugos-id'])) {
    header("Location: index.php");
    exit();
}

$paslaugosId = $_GET['paslaugos-id'] ?? null;

if ($paslaugosId) {
    $stmt = $mysqli->prepare("SELECT meistro_id FROM MeistrasPaslaugos WHERE paslaugos_id = ?");
    $stmt->bind_param('i', $paslaugosId);
    $stmt->execute();


    $result = $stmt->get_result();
    $meistrai = [];
    while ($row = $result->fetch_assoc()) {
        $meistrai[] = $row['meistro_id'];
    }

    #get mechanic profiles
    $meistruProfiliai = [];
    foreach($meistrai as $meistro_id) {
        $stmt = $mysqli->prepare("SELECT * FROM Naudotojai WHERE naudotojo_id = ?");
        $stmt->bind_param('i', $meistro_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $meistras = $result->fetch_assoc();
        $meistruProfiliai[] = $meistras;
    }


}

include 'header.php';
?>
<div class="container">
    <h1 class="mb-4">Pasirinkite meistrą</h1>
    <form id="mechanic-form" action="rezervacija.php" method="get">
        <input type="hidden" name="meistro_id" id="pasirinktas-meistro-id" required>
        <input type="hidden" name="paslaugos-id" value="<?php echo $paslaugosId; ?>">
            <?php
                foreach($meistruProfiliai as $meistras) {
                    
                    $meistroId = $meistras['naudotojo_id'];
                    $meistroVardas = $meistras['vardas'];
                    $aprasymas = $meistras['aprasymas'] ?? "Aprašymas nepateiktas.";
                    $nuotrauka = ($meistras['nuotrauka'] == "" ? 'nuotraukos/meistras.jpg' : $meistras['nuotrauka']);

                    $stmt = $mysqli->prepare("SELECT ROUND(AVG(rating), 2) as vidurkis, COUNT(rating) as kiekis FROM Ratings WHERE meistro_id = ?");
                    $stmt->bind_param("i", $meistroId);
                    $stmt->execute();
                    $ratingQuery = $stmt->get_result();
                    $ratingResult = $ratingQuery->fetch_assoc();
                    $rating = $ratingResult['vidurkis'] ?? 0;
                    $kiekis = $ratingResult['kiekis'] ?? 0;

                    echo "<div class='col-lg-4 col-md-6 mb-4'>";
                    echo "<div id='meistro-profilis-$meistroId' class='card h-100 border-0 shadow' onclick='pasirinktiMeistra($meistroId)'>";
                    echo "<img src='$nuotrauka' class='card-img-top img-fluid' alt='Meistro nuotrauka'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title text-primary'>$meistroVardas</h5>";
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