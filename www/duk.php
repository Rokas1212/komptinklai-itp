<?php
include 'header.php';
include 'db.php';

$sql = "SELECT * FROM DUK";
$duk = $mysqli->query($sql);
?>

<div class="container">
    <h1>Dažniausiai užduodami klausimai</h1>
    <?php
        echo "<hr>";
        while($row = $duk->fetch_assoc()) {
            echo "<h2>" . $row['klausimas'] . "</h2>";
            echo "<p>" . $row['atsakymas'] . "</p>";
            echo "<hr>";
        }
    ?>
</div>

<?php include 'footer.php'; ?>