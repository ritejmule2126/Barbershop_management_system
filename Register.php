<?php
session_start();
include 'db_connect.php';

// Connect to the database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the registration form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); // Hash the password for security

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO customer (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $phone, $password);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Registration successful, redirect to login page with a popup message
        echo "<script>
                alert('Registration successful! Welcome, " . htmlspecialchars($username) . ".');
                window.location.href = 'index.html';
              </script>";
    } else {
        // Handle errors
        echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href = 'register.html';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
