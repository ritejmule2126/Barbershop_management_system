<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; // Ensure this file exists

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are provided
    if (empty($username) || empty($password)) {
        die("Please enter both username and password.");
    }

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if login is successful
    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username; // Store session
        header("Location: admin_dashboard.php"); // Redirect to admin panel
        exit();
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}
?>
