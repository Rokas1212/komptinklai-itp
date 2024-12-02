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

$marke = $_POST['marke'] ?? null;
$modelis = $_POST['modelis'] ?? null;
$metai_nuo = $_POST['metai_nuo'] ?? null;
$metai_iki = $_POST['metai_iki'] ?? null;

if($marke && $modelis && $metai_nuo && $metai_iki) {
    $stmt = mysqli_prepare($mysqli, "INSERT INTO Automobilis (marke, modelis, metai_nuo, metai_iki) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssii", $marke, $modelis, $metai_nuo, $metai_iki);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: vadybininkas.php");
}

include 'header.php';
?>

<div class="container">
    <h1>Vadybininko meniu</h1>
    
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#meistroModal">
        Registruoti meistrą
    </button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paslaugosModal">
        Pridėti paslaugą
    </button>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#automobilisModal">
        Pridėti automobilį
    </button>

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
    
    <div class="container">
        <h2>Aptarnaujamų automobilių sąrašas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Markė</th>
                    <th>Modelis</th>
                    <th>Metai nuo</th>
                    <th>Metai iki</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = mysqli_prepare($mysqli, "SELECT marke, modelis, metai_nuo, metai_iki FROM Automobilis");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['marke'] . "</td>";
                    echo "<td>" . $row['modelis'] . "</td>";
                    echo "<td>" . $row['metai_nuo'] . "</td>";
                    echo "<td>" . $row['metai_iki'] . "</td>";
                    echo "</tr>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
            </table>
    </div>

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
                        <input type="password" class="form-control" id="slaptazodis" name="slaptazodis" required
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
                        title="Slaptažodyje turi būti mažiausiai 8 simboliai, didžioji raidė, mažoji raidė, skaičius ir specialus simbolis">
                    </div>
                    <div class="form-group">
                        <label for="telefono_numeris">Telefono numeris:</label>
                        <input type="tel" class="form-control" id="telefono_numeris" name="telefono_numeris" required
                        pattern="^\+?[0-9]{8,15}$"
                        title="Įveskite galiojantį telefono numerį (8-15 skaitmenų, gali prasidėti +)">
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

<div class="modal fade" id="automobilisModal" tabindex="-1" aria-labelledby="automobilisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="automobilisModalLabel">Automobilio registracija</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="vadybininkas.php" method="post">
                    <div class="form-group">
                        <label for="marke">Marke:</label>
                        <input type="text" class="form-control" id="marke" name="marke" required>
                    </div>
                    <div class="form-group">
                        <label for="modelis">Modelis:</label>
                        <input type="text" class="form-control" id="modelis" name="modelis" required>
                    </div>
                    <div class="form-group">
                        <label for="metai_nuo">Metai nuo:</label>
                        <input 
                            type="number" 
                            id="metai_nuo" 
                            name="metai_nuo" 
                            class="form-control" 
                            min="1901" 
                            max="2100" 
                            step="1" 
                            placeholder="Įveskite metus nuo kurių pradėtas gaminti modelis" 
                            required>
                    </div>
                    <div class="form-group">
                        <label for="metai_iki">Metai iki:</label>
                        <input 
                            type="number" 
                            id="metai_iki" 
                            name="metai_iki" 
                            class="form-control" 
                            min="1902" 
                            max="2100" 
                            step="1" 
                            placeholder="Įveskite metus iki kurių gamintas modelis" 
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridėti automobilį</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>