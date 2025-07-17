<?php
session_start();
include 'db_connect.php';

// Connect to the database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"]; // Can be username or email
    $password = $_POST["password"];

    // Query to fetch user details
    $sql = "SELECT * FROM customer WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start a session and save user information
            session_start();
            $_SESSION['user'] = $user;

            // Redirect to another page (e.g., dashboard.php)
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid password: Display a popup and redirect to index.html
            echo "<script>
                alert('Invalid password. Please try again.');
                window.location.href = 'index.html';
                </script>";
        }
    } else {
        // No user found: Display a popup and redirect to index.html
        echo "<script>
            alert('No user found with the given username or email.');
            window.location.href = 'index.html';
            </script>";
    }

    $stmt->close();
}

$conn->close();
?>
