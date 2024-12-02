<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != 'POST' && !isset($_GET['paslaugos-id'])) {
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

#if request method == post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meistroId = $_POST['meistro_id'];
    $selectedDate = $_POST['selected_date'];
    $selectedTime = $_POST['selected_time'];
    $paslaugosId = $_POST['paslaugos-id'];
    $automobilisId = $_POST['automobilis_id'];

    #check if time is within 09:00:00 and 18:00:00
    if ($selectedTime < '09:00:00' || $selectedTime > '18:00:00') {
        die('Laikas turi būti tarp 09:00 ir 18:00.');
    }

    #check if minutes 00 and seconds 00
    $timeParts = explode(':', $selectedTime);

    if (count($timeParts) !== 3 || $timeParts[1] !== '00' || $timeParts[2] !== '00') {
        die('Laikas turi būti nustatytas su minutėmis ir sekundėmis kaip 00:00.');
    }

    $selectedDateTime = new DateTime($selectedDate . ' ' . $selectedTime);
    $now = new DateTime();

    if ($selectedDateTime < $now) {
        die('Negalima rezervuoti praeityje.');
    }

    $stmt = $mysqli->prepare("INSERT INTO Rezervacijos (meistro_id, paslaugos_id, rezervacijos_data, rezervacijos_laikas, kliento_id, automobilis_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iisssi', $meistroId, $paslaugosId, $selectedDate, $selectedTime, $_SESSION['naudotojoId'], $automobilisId);

    if ($stmt->execute()) {
        header("Location: rezervacijos.php");
        exit();
    } else {
        die('Klaida įrašant rezervaciją: ' . $stmt->error);
    }
}

# fetch cars
$stmt = $mysqli->prepare("SELECT * FROM Automobilis");
$stmt->execute();
$carsResult = $stmt->get_result();
$cars = $carsResult->fetch_all(MYSQLI_ASSOC);

include 'header.php';
?>
<div class="container">
    <h1 class="mb-4">Pasirinkite meistrą</h1>
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
            echo "<div id='meistro-profilis-$meistroId' class='card h-100 border-0 shadow' data-toggle='modal' data-target='#mechanicModal'>";
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
        if(empty($meistruProfiliai)) {
            echo "<h3>Nėra meistrų šiai paslaugai</h3>";
        }
    ?>
</div>

<div class="modal fade" id="mechanicModal" tabindex="-1" aria-labelledby="mechanicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mechanicModalLabel">Pasirinkti laiką</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="time-selection-form" action="rezervacija.php" method="post">
                    <input type="hidden" name="meistro_id" id="modal-meistro-id">
                    <input type="hidden" name="paslaugos-id" value="<?php echo $paslaugosId; ?>">
                    <input type="hidden" name="selected_time" id="selected-time">

                    <div class="form-group">
                        <label for="selected-date">Pasirinkite datą:</label>
                        <input type="date" class="form-control" id="selected-date" name="selected_date" required>
                    </div>

                    <div class="form-group">
                        <label>Galimi laikai:</label>
                        <table class="table table-bordered" id="available-times-table">
                            <thead>
                                <tr>
                                    <th>Laikas</th>
                                    <th>Veiksmas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- time slots -->
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label for="selected-car">Pasirinkite automobilį:</label>
                        <select class="form-control" id="selected-car" name="automobilis_id" required>
                            <option value="" disabled selected>Pasirinkite automobilį</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo $car['id']; ?>">
                                <?php  echo $car['marke'] . ' ' . $car['modelis'] . ' ' . $car['metai_nuo'] . '-' . $car['metai_iki']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Patvirtinti</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="includes/rezervacija.js"></script>
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