<?php
// registracija.php

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

// Example usage
$userId = kurtiVartotoja("Jonas", "jonas1@example.com", "password123", "+37060000000", "meistras");
echo "Baxurs sukurc: " . $userId;
?>

<!DOCTYPE html>
<html lang="lt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <title>Autoservisas</title>
</head>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Autoservisas</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Pagrindinis</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="rezervacija.php">Paslaugos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="duk.php">DUK</a>
      </li>
    </ul>
  </div>
  <span class="navbar-text">
    Rokas Čiuplinskas IFD-2
  </span>
</nav>
<body>
    
</body>
</html>
