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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rezervacijos Identifikacinis Kodas</th>
                <th>Data</th>
                <th>Laikas</th>
                <th>Kaina</th>
                <th>Paslauga</th>
                <th>Automobilis</th>
                <th>Meistras</th>
                <th>Įvertink</th>
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
            $rating_query = mysqli_query($mysqli, "SELECT rating FROM Ratings WHERE meistro_id = " . $rezervacija['meistro_id'] . " AND kliento_id = $naudotojo_id");
            $rating = mysqli_fetch_assoc($rating_query)['rating'] ?? 0; // Default to 0 if no rating exists
            $automobilis = mysqli_query($mysqli, "SELECT * FROM Automobilis WHERE id = " . $rezervacija['automobilis_id']);
            $automobilis = mysqli_fetch_assoc($automobilis);
            echo "<tr>";
            echo "<td>" . $rezervacija['rezervacijos_id'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_data'] . "</td>";
            echo "<td>" . $rezervacija['rezervacijos_laikas'] . "</td>";
            echo "<td>" . $paslauga['kaina'] . "</td>";
            echo "<td>" . $paslauga['paslaugos_pavadinimas'] . "</td>";
            echo "<td>" . $automobilis['marke'] . ' ' . $automobilis['modelis'] . ' ' . $automobilis['metai_nuo'] . ' - ' . $automobilis['metai_iki'] . "</td>";
            echo "<td>" . $meistras['vardas'] . "</td>";
            if($rezervacija['rezervacijos_data'] < date('Y-m-d')) {
                echo "<td>
                        <button type='button' class='btn btn-link' data-toggle='modal' data-target='#ivertinimoModal'
                            data-meistro-id='" . $rezervacija['meistro_id'] . "' 
                            data-rating='" . $rating . "'
                            data-kliento-id='" . $naudotojo_id . "'>
                            Įvertinti
                        </button>
                        <!--// -->
                     </td>";
            } else {
                echo "<td> (po paslaugos) </td>";
            }
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    
</div>

<div class="modal fade" id="ivertinimoModal" tabindex="-1" aria-labelledby="ivertinimoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ivertinimoModalLabel">Meistro įvertinimas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="ivertinimai.php" method="post">
                    <input type="hidden" name="meistro_id" value="">
                    <input type="hidden" name="rating" value="0">
                    <i class="fa-regular fa-star" id="star-1" data-value="1"></i>
                    <i class="fa-regular fa-star" id="star-2" data-value="2"></i>
                    <i class="fa-regular fa-star" id="star-3" data-value="3"></i>
                    <i class="fa-regular fa-star" id="star-4" data-value="4"></i>
                    <i class="fa-regular fa-star" id="star-5" data-value="5"></i>
                    <button type="submit" class="btn btn-primary">Įšsaugoti įvertinimą</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Handle star rating click events
document.querySelectorAll('.fa-star').forEach(star => {
    star.addEventListener('click', function () {
        const rating = this.getAttribute('data-value'); // Get the clicked star's rating value

        // Update the hidden input with the selected rating
        document.querySelector('input[name="rating"]').value = rating;

        // Reset all stars in the modal
        this.parentNode.querySelectorAll('.fa-star').forEach(s => {
            s.classList.remove('fa-solid');
            s.classList.add('fa-regular');
            s.style.color = '#ddd';
        });

        // Highlight stars up to the selected one
        this.classList.add('fa-solid');
        this.style.color = 'gold';
        let previousSibling = this.previousElementSibling;
        while (previousSibling) {
            previousSibling.classList.add('fa-solid');
            previousSibling.style.color = 'gold';
            previousSibling = previousSibling.previousElementSibling;
        }
    });
});

// Handle opening the modal and populating it with mechanic-specific data
document.querySelectorAll('[data-toggle="modal"]').forEach(button => {
    button.addEventListener('click', function () {
        const meistroId = this.getAttribute('data-meistro-id'); // Mechanic ID
        const currentRating = this.getAttribute('data-rating'); // Current rating

        // Populate the hidden inputs in the modal with mechanic data
        document.querySelector('input[name="meistro_id"]').value = meistroId;
        document.querySelector('input[name="rating"]').value = currentRating;

        // Reset all stars in the modal
        document.querySelectorAll('.fa-star').forEach(star => {
            star.classList.remove('fa-solid');
            star.classList.add('fa-regular');
            star.style.color = '#ddd';
        });

        // Highlight stars up to the current rating
        for (let i = 1; i <= currentRating; i++) {
            const star = document.getElementById(`star-${i}`);
            star.classList.add('fa-solid');
            star.style.color = 'gold';
        }
    });
});

// Submit the rating via AJAX
document.querySelector('form[action="ivertinimai.php"]').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the form from refreshing the page

    const formData = new FormData(this);

    fetch('ivertinimai.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close the modal
            $('#ivertinimoModal').modal('hide');

            // Optional: Show success message
            console.log(data.message);
        } else {
            // Handle error
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});


</script>
<?php
include 'footer.php';
?>