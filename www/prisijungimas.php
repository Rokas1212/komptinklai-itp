<?php 
session_start();
include 'db.php';

if (isset($_SESSION['naudotojoId'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $el_pastas = $_POST['el_pastas'];
    $prisijungimo_slaptazodis = $_POST['prisijungimo_slaptazodis'];
    
    $sql = "SELECT naudotojo_id, vardas, slaptazodis, vaidmuo FROM Naudotojai WHERE el_pastas = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $el_pastas);
    $stmt->execute();
    $stmt->store_result();
    
    $stmt->bind_result($naudotojoId, $vardas, $hashedPassword, $vaidmuo);
    $stmt->fetch();
    
    if ($stmt->num_rows == 1 && password_verify($prisijungimo_slaptazodis, $hashedPassword)) {
        $_SESSION['naudotojoId'] = $naudotojoId;
        $_SESSION['vardas'] = $vardas;
        $_SESSION['vaidmuo'] = $vaidmuo;
        header("Location: index.php");
        exit();
    } else {
        $pranesimas = 'Neteisingas el.paštas arba slaptažodis.';
    }
}

include 'header.php';

?>

<div class="container">
    <h1>Prisijungimas</h1>
    <form action="prisijungimas.php" method="post">
        <div class="form-group">
            <label for="el_pastas">el.paštas:</label>
            <input type="text" class="form-control" id="el_pastas" name="el_pastas" required value="<?php echo isset($el_pastas) ? $el_pastas : ''; ?>">
        </div>
        <div class="form-group">
            <label for="prisijungimo_slaptazodis">Slaptažodis:</label>
            <input type="password" class="form-control" id="prisijungimo_slaptazodis" name="prisijungimo_slaptazodis" required value="<?php echo isset($prisijungimo_slaptazodis) ? $prisijungimo_slaptazodis : '';?>">
        </div>
        <button type="submit" class="btn btn-primary">Prisijungti</button>
        <a href="registracija.php" class="btn btn-link">Registruotis</a>
    </form>
    <?php if (isset($pranesimas)): ?>
    <div class="alert alert-danger mt-3" role="alert">
        <?php echo $pranesimas; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
