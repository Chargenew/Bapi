<?php
header('Content-Type: application/json');
require 'db.php';
date_default_timezone_set('Asia/Kolkata');

$method = $_SERVER['REQUEST_METHOD'];

$response = [];

switch ($method) {
    case 'GET':
        if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
            $startDate = $conn->real_escape_string($_GET["start_date"]);
            $endDate = $conn->real_escape_string($_GET["end_date"]);
        }
}
?>