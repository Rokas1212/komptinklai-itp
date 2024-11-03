<?php
session_start();
include 'db.php';

if (!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}
else if ($_SESSION['vaidmuo'] != 'meistras') {
    header("Location: index.php");
    exit();
}

$start = strtotime('09:00');
$end = strtotime('18:00');

while ($start < $end) {
    $timeSlot = date("H:i:s", $start);
    
    $availableTimes[] = $timeSlot;
    
    $start = strtotime('+60 minutes', $start);
}

$meistro_id = $_SESSION['naudotojoId'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diena = $_POST['diena'];
    $laikas = $_POST['laikas'];
    

    #patikrinti ar diena ir laikas nera jau rezervuoti
    $sql0 = "SELECT * FROM Prieinamumas WHERE meistro_id = ? AND diena = ? AND laikas = ?";
    $stmt0 = $mysqli->prepare($sql0);
    $stmt0->bind_param('iss', $meistro_id, $diena, $laikas);
    $stmt0->execute();
    $stmt0->store_result();
    $sql1 = "SELECT * FROM Rezervacijos WHERE meistro_id = ? AND rezervacijos_data = ? AND rezervacijos_laikas = ?";
    $stmt1 = $mysqli->prepare($sql1);
    $stmt1->bind_param('iss', $meistro_id, $diena, $laikas);
    $stmt1->execute();
    $stmt1->store_result();
    if ($stmt1->num_rows > 0 || $stmt0->num_rows > 0) {
        $error = "Šiuo metu jau turi rezervaciją, tad užsidaryti laiko nebegali.";
        echo "<script type='text/javascript'>alert('$error');</script>";
    } else {
        $sql = "INSERT INTO Prieinamumas (meistro_id, diena, laikas) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('iss', $meistro_id, $diena, $laikas);
        $stmt->execute();
        
        header("Location: meistras.php");
        exit();
    }
}

include 'header.php';
?>

<div class="container">
    <h1> Pasirinkti užimtumą </h1>
    <form action="meistras.php" method="post">
        <div class="form-group">
            <label for="diena">Diena:</label>
            <input type="date" class="form-control" id="diena" name="diena" required>
        </div>
        <div class="form-group">
            <label for="laikas">Laikas</label>
            <select class="form-control" id="laikas" name="laikas" required>
                <option value="">-- Pasirinkite laiką --</option>
                <?php
                foreach ($availableTimes as $time) {
                    echo "<option value='$time'>$time</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Patvirtinti</button>
    </form>
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
        <h2> Mano neprieinami laikai </h2>
        <thead>
            <tr>
                <th>Diena</th>
                <th>Laikas</th>
                <th>Veiksmai</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $uzimtumai = mysqli_query($mysqli, "SELECT * FROM Prieinamumas WHERE meistro_id = $meistro_id ORDER BY diena ASC");
            while ($uzimtumas = mysqli_fetch_assoc($uzimtumai)) {
                if ($uzimtumas['diena'] < date('Y-m-d')) {
                    continue;
                }
                echo "<tr>";
                echo "<td>" . $uzimtumas['diena'] . "</td>";
                echo "<td>" . $uzimtumas['laikas'] . "</td>";
                echo "<td><a href='istrinti.php?id=" . $uzimtumas['prieinamumo_id'] . "'>Ištrinti (kol kas neveikia)</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>



<?php include 'footer.php'; ?>


