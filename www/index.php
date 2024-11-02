<?php
session_start();

$isLoggedIn = isset($_SESSION['naudotojoId']);
include 'header.php';
?>

<div class="container">
    <?php if ($isLoggedIn): ?>
        <h1>Sveikas, <?php echo htmlspecialchars($_SESSION['vardas']); ?>!</h1>
        <p>Esate prisijungę.</p>
        <a href="logout.php" class="btn btn-secondary">Atsijungti</a>
    <?php else: ?>
        <h1>Labas!</h1>
        <p><a href="prisijungimas.php">Prisijunkite</a> arba <a href="registracija.php">prisiregistruokite</a></p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>