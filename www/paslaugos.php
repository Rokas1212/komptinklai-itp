<?php
include 'db.php';
include 'header.php';
?>

<h1>Pasirinkite meistrą</h1>

<form action="rezervacija.php" method="get">
    <div class="form-group">
        <label for="meistro_id">Pasirinkite meistrą:</label>
        <select class="form-control" id="meistro_id" name="meistro_id" onchange="loadMechanicProfile()">
            <option value="">-- Pasirinkite meistrą --</option>
            <?php
            $result = mysqli_query($mysqli, "SELECT naudotojo_id, vardas FROM Naudotojai WHERE vaidmuo = 'meistras'");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['naudotojo_id'] . "'>" . $row['vardas'] . "</option>";
            }
            ?>
        </select>
    </div>
    <div id="mechanic-profile" class="mt-3 p-3 border rounded" style="display: none;">
        <!-- mechaniko profilis -->
    </div>
    <button type="submit" class="btn btn-primary">Tęsti</button>
</form>

<script>
function loadMechanicProfile() {
    const meistroId = document.getElementById("meistro_id").value;
    if (meistroId === "") {
        document.getElementById("mechanic-profile").style.display = "none";
        return;
    }

    fetch(`meistro_profilis.php?meistro_id=${meistroId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("mechanic-profile").innerHTML = data;
            document.getElementById("mechanic-profile").style.display = "block";
        })
        .catch(error => console.error('Error fetching mechanic profile:', error));
}
</script>

<?php include 'footer.php'; ?>