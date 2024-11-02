<?php
session_start();
include 'db.php';

function kurtiVartotoja($vardas, $el_pastas, $slaptazodis, $telefono_numeris, $vaidmuo) {
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
    $naudotojoId = kurtiVartotoja($vardas, $el_pastas, $slaptazodis, $telefono_numeris, "klientas");

    if ($naudotojoId) {
        $_SESSION['naudotojoId'] = $naudotojoId;
        $_SESSION['vardas'] = $vardas;

        header("Location: index.php");
        exit();
    } else {
        echo "Registracijos klaida. Bandykite dar kartą.";
    }
}
include 'header.php';
?>

<div class="container">
    <h1>Registracija</h1>
    <form action="registracija.php" method="post">
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
        <button type="submit" class="btn btn-primary">Registruotis</button>
    </form>
</div>

<?php include 'footer.php'; ?>