<?php
session_start();
include 'db.php';

if(!isset($_SESSION['naudotojoId']) || $_SESSION['vaidmuo'] != 'vadybininkas') {
    header("Location: index.php");
    exit();
}

function kurtiMeistra($vardas, $el_pastas, $slaptazodis, $telefono_numeris, $vaidmuo="meistras") {
    global $mysqli;
    $hash = password_hash($slaptazodis, PASSWORD_BCRYPT);
    $stmt = mysqli_prepare($mysqli, "INSERT INTO Naudotojai (vardas, el_pastas, slaptazodis, telefono_numeris, vaidmuo) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $vardas, $el_pastas, $hash, $telefono_numeris, $vaidmuo);
    mysqli_stmt_execute($stmt);
    $insertedId = mysqli_insert_id($mysqli);
    mysqli_stmt_close($stmt);
    return $insertedId;
}

$vardas = $_POST['vardas'] ?? null;
$el_pastas = $_POST['el_pastas'] ?? null;
$slaptazodis = $_POST['slaptazodis'] ?? null;
$telefono_numeris = $_POST['telefono_numeris'] ?? null;

if ($vardas && $el_pastas && $slaptazodis && $telefono_numeris) {
    $naudotojoId = kurtiMeistra($vardas, $el_pastas, $slaptazodis, $telefono_numeris);

    if ($naudotojoId) {
        header("Location: vadybininkas.php");
        exit();
    } else {
        echo "Registracijos klaida. Bandykite dar kartą.";
    }
}

$paslaugos_pavadinimas = $_POST['paslaugos_pavadinimas'] ?? null;
$aprasymas = $_POST['aprasymas'] ?? null;
$kaina = $_POST['kaina'] ?? null;

if($paslaugos_pavadinimas && $aprasymas && $kaina) {
    $stmt = mysqli_prepare($mysqli, "INSERT INTO Paslaugos (paslaugos_pavadinimas, aprasymas, kaina) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssd", $paslaugos_pavadinimas, $aprasymas, $kaina);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: vadybininkas.php");
}

include 'header.php';
?>

<div class="container">
    <h1>Vadybininko meniu</h1>

    <div class="container">
        <h2>Meistrų sąrašas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Vardas</th>
                    <th>El. paštas</th>
                    <th>Telefono numeris</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = mysqli_prepare($mysqli, "SELECT vardas, el_pastas, telefono_numeris FROM Naudotojai WHERE vaidmuo = 'meistras'");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['vardas'] . "</td>";
                    echo "<td>" . $row['el_pastas'] . "</td>";
                    echo "<td>" . $row['telefono_numeris'] . "</td>";
                    echo "</tr>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Paslaugų sąrašas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Paslaugos pavadinimas</th>
                    <th>Aprašymas</th>
                    <th>Kaina</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = mysqli_prepare($mysqli, "SELECT paslaugos_pavadinimas, aprasymas, kaina FROM Paslaugos");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['paslaugos_pavadinimas'] . "</td>";
                    echo "<td>" . $row['aprasymas'] . "</td>";
                    echo "<td>" . $row['kaina'] . "</td>";
                    echo "</tr>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
    </div>


    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#meistroModal">
        Registruoti meistrą
    </button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paslaugosModal">
        Pridėti paslaugą
    </button>
</div>

<div class="modal fade" id="meistroModal" tabindex="-1" aria-labelledby="meistroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meistroModalLabel">Meistro registracija</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="vadybininkas.php" method="post">
                    <div class="form-group">
                        <label for="vardas">Vardas:</label>
                        <input type="text" class="form-control" id="vardas" name="vardas" required>
                    </div>
                    <div class="form-group">
                        <label for="el_pastas">El. paštas:</label>
                        <input type="email" class="form-control" id="el_pastas" name="el_pastas" required>
                    </div>
                    <div class="form-group">
                        <label for="slaptazodis">Slaptažodis:</label>
                        <input type="password" class="form-control" id="slaptazodis" name="slaptazodis" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono_numeris">Telefono numeris:</label>
                        <input type="tel" class="form-control" id="telefono_numeris" name="telefono_numeris" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Registruoti meistrą</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paslaugosModal" tabindex="-1" aria-labelledby="paslaugosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paslaugosModalLabel">Paslaugų pridėjimas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="vadybininkas.php" method="post">
                    <div class="form-group">
                        <label for="paslaugos_pavadinimas">Paslaugos pavadinimas:</label>
                        <input type="text" class="form-control" id="paslaugos_pavadinimas" name="paslaugos_pavadinimas" required>
                    </div>
                    <div class="form-group">
                        <label for="aprasymas">Aprašymas:</label>
                        <textarea class="form-control" id="aprasymas" name="aprasymas" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="kaina">Kaina:</label>
                        <input type="number" class="form-control" id="kaina" name="kaina" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridėti paslaugą</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>