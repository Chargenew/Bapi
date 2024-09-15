<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['isLoggedin'])) {
    // If not logged in, redirect to login page
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wizard</title>
    <link rel="stylesheet" href="../style/style.css">
    
    <!-- Bootstrap links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" aria-current="page" href="./home.php">Home</a>
                    <a class="nav-link" href="./wizards.php">Wizards</a>
                    <a class="nav-link" href="../index.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <br>
    <!-- Form -->
    <form onsubmit="return false;">
        <div class="container mt-4">
            <div class="row">
                <div class="col"><input type="date" id="start_date1" class="form-control"></div>
                <div class="col"><input type="date" id="end_date1" class="form-control"></div>
                <div class="col">
                    <button onclick="fetchTables()" class="btn btn-primary">Search</button>
                </div>
            </div>
            <p class="mt-3">Selected Date Range: <span id="selectedDateRange1"></span></p>
        </div>
    </form>

    <h1>FUND IN</h1>
    <div class="container mt-4">
        <table class="table">
            <thead id="thead1"></thead>
            <tbody id="tbody1"></tbody>
        </table>
    </div>

    <br>
    <h1>FUND OUT</h1>
    <div class="container mt-4">
        <table class="table">
            <thead id="thead2"></thead>
            <tbody id="tbody2"></tbody>
        </table>
    </div>

    <br>
    <h1>BANK BALANCE</h1>
    <div class="container mt-4">
        <table class="table">
            <thead id="thead3"></thead>
            <tbody id="tbody3"></tbody>
        </table>
    </div>

    <br>
    <h1>EXPENSES</h1>
    <div class="container mt-4">
        <table class="table">
            <thead id="thead4"></thead>
            <tbody id="tbody4"></tbody>
        </table>
    </div>

    <script src="../controller/wizardScript.js"></script>
</body>

</html>