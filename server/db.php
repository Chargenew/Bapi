<?php
// $servername = "shareddb-b.hosting.stackcp.net";
// $username = "BAPI_Chargenew-3533f9e9";
// $password = "Qwerty@99";
// $dbname = "BAPI_Chargenew-3533f9e9";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BAPI_Chargenew-3533f9e9";

date_default_timezone_set('Asia/Kolkata');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$conn->query("SET time_zone = '+05:30'");
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
