<?php
$servername = "sql113.infinityfree.com";
$db_username = "if0_38301835";  // Correct variable name
$db_password = "9769788232";    // Correct variable name
$dbname = "if0_38301835_barbershop_management";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
