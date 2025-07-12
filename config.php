<?php
$host = "localhost";
$user = "root"; // Change if you have a different DB user
$pass = ""; // Set password if needed
$dbname = "moocs_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
