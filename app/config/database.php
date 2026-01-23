<?php
$server = "localhost";
$user = "root";
$password = "";
$dbname = "gallon_inventory_db";

$conn = new mysqli($server, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Hapus echo "connection is done" di sini
?>
