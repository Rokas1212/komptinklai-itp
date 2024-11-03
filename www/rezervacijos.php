<?php
include 'db.php';
session_start();

if(!isset($_SESSION['naudotojoId'])) {
    header("Location: prisijungimas.php");
    exit();
}

include 'header.php';
?>


<div class='container'>
    <h1>Mano rezervacijos</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rezervacijos Identifikacinis Kodas</th>
                <th>Data</th>
                <th>Laikas</th>
                <th>Kaina</th>
                <th>Paslauga</th>
                <th>Meistras</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $naudotojo_id = $_SESSION['naudotojoId'];
        $rezervacijos = mysqli_query($mysqli, "SELECT * FROM Rezervacijos WHERE kliento_id = $naudotojo_id");
        while ($rezervacija = mysqli_fetch_assoc($rezervacijos)) {
            $meistras = mysqli_query($mysqli, "SELECT vardas FROM Naudotojai WHERE naudotojo_id = " . $rezervacija['meistro_id']);
            $meistras = mysqli_fetch_assoc($meistras);
            $paslauga = mysqli_query($mysqli, "SELECT paslaugos_pavadinimas, kaina FROM Paslaugos WHERE paslaugos_id = " . $rezervacija['paslaugos_id']);
            $paslauga = mysqli_fetch_assoc($paslauga);
            echo "<tr>";
            echo "<td>" . $rezervacija['rezervacijos_id'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_data'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_laikas'] . "</td>";
            echo "<td>" . $paslauga['kaina'] . "</td>";
            echo "<td>" . $paslauga['paslaugos_pavadinimas'] . "</td>";
            echo "<td>" . $meistras['vardas'] . "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

</div>



<?php
include 'footer.php';
?>