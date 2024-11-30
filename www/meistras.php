<?php
session_start();
include 'db.php';
include 'mechanicWeek.php';
$daysInLithuanian = [
    'Monday' => 'Pirmadienis',
    'Tuesday' => 'Antradienis',
    'Wednesday' => 'Trečiadienis',
    'Thursday' => 'Ketvirtadienis',
    'Friday' => 'Penktadienis',
    'Saturday' => 'Šeštadienis',
    'Sunday' => 'Sekmadienis',
];
if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}
else if ($_SESSION['vaidmuo'] != 'meistras') {
    header("Location: index.php");
    exit();
}
if(isset($_GET['success'])) {
    $success = "Paslaugos išsaugotos sėkmingai.";
    echo "<script type='text/javascript'>alert('$success');</script>";
}

$meistro_id = $_SESSION['naudotojoId'];
// šios savaitės rezervacijos mechanicWeek.php
$weekDays = getWeekdays();
$timeSlots = getTimeSlots();
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
$reservations = getReservationsForWeek($mysqli, $meistro_id, $startOfWeek, $endOfWeek);
$unavailabilities = getUnavailableTimeSlotsWeek($mysqli, $meistro_id, $startOfWeek, $endOfWeek);

$start = strtotime('09:00');
$end = strtotime('18:00');

$availableTimes = getWorkingHours($start, $end);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $laikas = $_POST['laikas'];
    

    #patikrinti ar data ir laikas nera jau rezervuoti
    $sql0 = "SELECT * FROM Prieinamumas WHERE meistro_id = ? AND data = ? AND laikas = ?";
    $stmt0 = $mysqli->prepare($sql0);
    $stmt0->bind_param('iss', $meistro_id, $data, $laikas);
    $stmt0->execute();
    $stmt0->store_result();
    $sql1 = "SELECT * FROM Rezervacijos WHERE meistro_id = ? AND rezervacijos_data = ? AND rezervacijos_laikas = ?";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param('iss', $meistro_id, $data, $laikas);
    $stmt1->execute();
    $stmt1->store_result();
    if ($stmt1->num_rows > 0 || $stmt0->num_rows > 0) {
        $error = "Šiuo metu jau turi rezervaciją, tad užsidaryti laiko nebegali.";
        echo "<script type='text/javascript'>alert('$error');</script>";
    } else {
        $sql = "INSERT INTO Prieinamumas (meistro_id, data, laikas) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('iss', $meistro_id, $data, $laikas);
        $stmt->execute();
        
        header("Location: meistras.php");
        exit();
    }
}

include 'header.php';
?>

<div class="container">
    <h1> Sveiki, <?php echo $_SESSION['vardas']; ?> </h1>

    <h2> Meistro Meniu </h2>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paslaugosModal" onclick="setMechanicId(<?php echo $meistro_id; ?>)">
        Redaguoti paslaugas
    </button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uzimtumasModal">
        Pridėti užimtumą
    </button>

</div>

<div class="container">
    <h2>Savaitės darbai</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Laikas</th>
                <?php foreach ($weekDays as $day): ?>
                    <th><?php echo $daysInLithuanian[date('l', strtotime($day))]; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timeSlots as $time): ?>
                <tr>
                    <td><?php echo $time; ?></td>
                    <?php foreach ($weekDays as $day): ?>
                        <td>
                            <?php
                            if (isset($reservations[$day][$time])) {
                                $serviceId = $reservations[$day][$time];
                                $serviceQuery = "SELECT paslaugos_pavadinimas FROM Paslaugos WHERE paslaugos_id = ?";
                                $serviceStmt = $mysqli->prepare($serviceQuery);
                                $serviceStmt->bind_param('i', $serviceId);
                                $serviceStmt->execute();
                                $serviceResult = $serviceStmt->get_result();
                                $service = $serviceResult->fetch_assoc();
                                echo htmlspecialchars($service['paslaugos_pavadinimas']);
                                $serviceStmt->close();
                            } elseif (isset($unavailabilities[$day]) && in_array($time, $unavailabilities[$day])) {
                                echo '<span style="color: red;">Užimtas</span>';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div class="container">
    <h2> Mano Darbai </h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rezervacijos Identifikacinis Kodas</th>
                <th>Data</th>
                <th>Laikas</th>
                <th>Kaina</th>
                <th>Paslauga</th>
                <th>Klientas</th>
                <th>Veiksmai</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $rezervacijos = mysqli_query($mysqli, "SELECT * FROM Rezervacijos WHERE meistro_id = $meistro_id ORDER BY rezervacijos_data ASC");
            while ($rezervacija = mysqli_fetch_assoc($rezervacijos)) {
            $paslauga = mysqli_query($mysqli, "SELECT paslaugos_pavadinimas, kaina FROM Paslaugos WHERE paslaugos_id = " . $rezervacija['paslaugos_id']);
            $paslauga = mysqli_fetch_assoc($paslauga);
            $klientas = mysqli_query($mysqli, "SELECT vardas FROM Naudotojai WHERE naudotojo_id = " . $rezervacija['kliento_id']);
            $klientas = mysqli_fetch_assoc($klientas);
            echo "<tr>";
            echo "<td>" . $rezervacija['rezervacijos_id'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_data'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_laikas'] . "</td>";
            echo "<td>" . $paslauga['kaina'] . "</td>";
            echo "<td>" . $paslauga['paslaugos_pavadinimas'] . "</td>";
            echo "<td>" . $klientas['vardas'] . "</td>";
            echo "<td><a href='atsaukti.php?id=" . $rezervacija['rezervacijos_id'] . "'>Atšaukti</a></td>";
            echo "</tr>";
            }
        ?>
        </tbody>
    </table>
    <table class="table table-striped">
        <h2>Mano neprieinami laikai</h2>
        <thead>
            <tr>
                <th>Data</th>
                <th>Laikas</th>
                <th>Veiksmai</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $uzimtumai = mysqli_query($mysqli, "SELECT * FROM Prieinamumas WHERE meistro_id = $meistro_id ORDER BY data ASC");
            while ($uzimtumas = mysqli_fetch_assoc($uzimtumai)) {
                if ($uzimtumas['data'] < date('Y-m-d')) {
                    continue;
                }
                echo "<tr id='row-{$uzimtumas['prieinamumo_id']}'>";
                echo "<td>" . $uzimtumas['data'] . "</td>";
                echo "<td>" . $uzimtumas['laikas'] . "</td>";
                echo "<td><button class='btn btn-danger btn-sm' onclick='deleteUnavailableTime(" . $uzimtumas['prieinamumo_id'] . ")'>Ištrinti</button></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="uzimtumasModal" tabindex="-1" aria-labelledby="uzimtumasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uzimtumasModalLabel">Pasirinkti užimtumą</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uzimtumasForm" action="meistras.php" method="post">
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" class="form-control" id="data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="laikas">Laikas</label>
                        <select class="form-control" id="laikas" name="laikas" required>
                            <option value="">-- Pasirinkite laiką --</option>
                            <?php foreach ($availableTimes as $time): ?>
                                <option value="<?php echo $time; ?>"><?php echo $time; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Uždaryti</button>
                <button type="submit" class="btn btn-primary" form="uzimtumasForm">Patvirtinti</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="paslaugosModal" tabindex="-1" aria-labelledby="paslaugosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paslaugosModalLabel">Mano paslaugos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paslaugosForm" action="saveMechanicServices.php" method="post">
                    <input type="hidden" name="meistro_id" id="meistro_id" value="">

                    <div id="servicesList">
                        <?php
                            $meistro_id = $_SESSION['naudotojoId'];

                            $services_query = "SELECT * FROM Paslaugos";
                            $services_result = mysqli_query($mysqli, $services_query);

                            $mechanic_services_query = "SELECT paslaugos_id FROM MeistrasPaslaugos WHERE meistro_id = $meistro_id";
                            $mechanic_services_result = mysqli_query($mysqli, $mechanic_services_query);

                            $mechanic_services = [];
                            while ($row = mysqli_fetch_assoc($mechanic_services_result)) {
                                $mechanic_services[] = $row['paslaugos_id'];
                            }

                            while ($service = mysqli_fetch_assoc($services_result)) {
                                $checked = in_array($service['paslaugos_id'], $mechanic_services) ? 'checked' : '';
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" name="services[]" value="' . $service['paslaugos_id'] . '" id="service-' . $service['paslaugos_id'] . '" ' . $checked . '>';
                                echo '<label class="form-check-label" for="service-' . $service['paslaugos_id'] . '">' . htmlspecialchars($service['paslaugos_pavadinimas']) . '</label>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-3">Išsaugoti</button>
                </form>
            </div>
        </div>
    </div>
</div>




<script src="includes/mechanicMenu.js"></script>

<style>
.table-bordered th, .table-bordered td {
    text-align: center;
    vertical-align: middle;
}
</style>


<?php include 'footer.php'; ?>


