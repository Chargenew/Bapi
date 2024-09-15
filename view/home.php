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
    <title>Bank Transactions Dashboard</title>
    <link rel="stylesheet" href="../style/style.css">
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

    <h1>Bank Transactions Dashboard</h1>
    <div class="table-container">
        <h2>Bank Balances</h2>
        <table>
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody id="balances-table"></tbody>
        </table>
    </div>

    <div class="form-container">
        <h2>Add / Update Transaction</h2>
        <form id="transaction-form">
            <input type="hidden" id="transaction-id" />
            <label for="transaction-type">Type:</label>
            <select id="transaction-type" onchange="populateCategory()"></select><br>
            <label for="category">Category:</label>
            <select id="category" onchange="populateSubCategory()"></select><br>
            <label for="sub-category">Sub Category:</label>
            <select id="sub-category"></select><br>
            <label for="bank">Bank:</label>
            <select id="bank"></select><br>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" step="0.01" /><br>
            <label for="reference-number">Reference Number:</label>
            <input type="text" id="reference-number" /><br>
            <label for="remarks">Remarks:</label>
            <input type="text" id="remarks" /><br>
            <label for="timestamp">Date and Time:</label>
            <input type="datetime-local" id="timestamp" /><br>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Daily Transactions</h2>
        <input type="date" id="transaction-date" value="<?php echo date('Y-m-d'); ?>" />
        <button onclick="populateTransactions()">Filter</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>Bank</th>
                    <th>Amount</th>
                    <th>Reference Number</th>
                    <th>Remarks</th>
                    <th>Timestamp</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="transactions-table"></tbody>
        </table>
    </div>

    <script src="../controller/homeScript.js"></script>
</body>

</html>