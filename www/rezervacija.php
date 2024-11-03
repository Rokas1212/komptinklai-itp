<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

$meistro_id = isset($_GET['meistro_id']) ? $_GET['meistro_id'] : null;

if (!$meistro_id) {
    echo "<p>Meistras nepasirinktas. Grįžkite į <a href='paslaugos.php'>meistro pasirinkimo</a> puslapį.</p>";
    include 'footer.php';
    exit();
}

$rezervacijos_data = isset($_POST['rezervacijos_data']) ? $_POST['rezervacijos_data'] : null;
$availableTimes = [];

if ($rezervacijos_data) {
    $unavailableQuery = "
        SELECT laikas, diena
        FROM Prieinamumas
        WHERE meistro_id = ? AND diena = ?";
    $stmt1 = mysqli_prepare($mysqli, $unavailableQuery);
    mysqli_stmt_bind_param($stmt1, "is", $meistro_id, $rezervacijos_data);
    mysqli_stmt_execute($stmt1);
    $unavailableResult = mysqli_stmt_get_result($stmt1);

    $unavailableTimes = [];
    while ($row = mysqli_fetch_assoc($unavailableResult)) {
        $unavailableTimes[] = $row['laikas'];
    }
    mysqli_stmt_close($stmt1);

    $reservationQuery = "
        SELECT rezervacijos_laikas
        FROM Rezervacijos
        WHERE meistro_id = ? AND rezervacijos_data = ?";
    $stmt2 = mysqli_prepare($mysqli, $reservationQuery);
    mysqli_stmt_bind_param($stmt2, "is", $meistro_id, $rezervacijos_data);
    mysqli_stmt_execute($stmt2);
    $reservationResult = mysqli_stmt_get_result($stmt2);
    
    $reservedTimes = [];
    while ($row = mysqli_fetch_assoc($reservationResult)) {
        $reservedTimes[] = $row['rezervacijos_laikas'];
    }
    mysqli_stmt_close($stmt2);

    $unavailableTimes = array_map(function($time) {
        return date("H:i", strtotime($time));
    }, $unavailableTimes);

    $reservedTimes = array_map(function($time) {
        return date("H:i", strtotime($time));
    }, $reservedTimes);

    $start = strtotime("9:00");
    $end = strtotime("18:00");

    while ($start < $end) {
        $timeSlot = date("H:i", $start);
        
        if (!in_array($timeSlot, $unavailableTimes, true) && !in_array($timeSlot, $reservedTimes, true)) {
            $availableTimes[] = $timeSlot;
        }
        
        $start = strtotime('+60 minutes', $start);
    }
}

$naudotojoId = $_SESSION['naudotojoId'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $rezervacijos_data && isset($_POST['rezervacijos_laikas'])) {
    $kliento_id = $naudotojoId;
    $paslaugos_id = $_POST['paslaugos_id'];
    $rezervacijos_laikas = $_POST['rezervacijos_laikas'];

    $stmt = mysqli_prepare($mysqli, "INSERT INTO Rezervacijos (kliento_id, meistro_id, paslaugos_id, rezervacijos_data, rezervacijos_laikas) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiiss", $kliento_id, $meistro_id, $paslaugos_id, $rezervacijos_data, $rezervacijos_laikas);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: rezervacijos.php");
    exit();
}
include 'header.php';
?>
<div class="container-sm">
    <h1>Rezervacija</h1>

    <form action="rezervacija.php?meistro_id=<?php echo $meistro_id; ?>" method="post">
        <div class="form-group">
            <label for="rezervacijos_data">Pasirinkite datą</label>
            <input type="date" class="form-control" id="rezervacijos_data" name="rezervacijos_data" value="<?php echo $rezervacijos_data; ?>" required onchange="this.form.submit()">
        </div>

        <?php if ($rezervacijos_data): ?>
        <div class="form-group">
            <label for="rezervacijos_laikas">Pasirinkite laiką</label>
            <select class="form-control" id="rezervacijos_laikas" name="rezervacijos_laikas" required>
                <option value="">-- Pasirinkite laiką --</option>
                <?php
                foreach ($availableTimes as $time) {
                    echo "<option value='$time'>$time</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="paslaugos_id">Paslauga</label>
            <select class="form-control" id="paslaugos_id" name="paslaugos_id" required>
                <option value="">-- Pasirinkite paslaugą --</option>
                <?php
                $result = mysqli_query($mysqli, "SELECT paslaugos_id, paslaugos_pavadinimas FROM Paslaugos");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['paslaugos_id'] . "'>" . $row['paslaugos_pavadinimas'] . "</option>";
                }
                ?>
            </select>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Rezervuoti</button>
    </form>
</div>
<?php include 'footer.php'; ?>