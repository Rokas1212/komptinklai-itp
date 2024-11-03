<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
    }

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 1rem;
    }
  </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                    <a class="nav-link" href="paslaugos.php">Paslaugos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="rezervacijos.php">Mano Rezervacijos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="duk.php">DUK</a>
                </li>
                <?php if (isset($_SESSION['vaidmuo']) && $_SESSION['vaidmuo'] == 'meistras'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="meistras.php">Meistro Meniu</a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['vaidmuo']) && $_SESSION['vaidmuo'] == 'vadybininkas'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="vadybininkas.php">Vadybininko Meniu</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <span class="navbar-text">
            Rokas Čiuplinskas IFD-2
        </span>
        </nav>
    </header>
<main>