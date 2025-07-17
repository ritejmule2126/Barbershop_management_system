<?php
session_start();
include 'db_connect.php';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $username = $_POST["username"];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ? AND username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to reset password page with the username as a URL parameter
        header("Location: reset_password.php?username=" . urlencode($username));
        exit();
    } else {
        echo "<script>alert('No user found with this email and username.'); window.location.href = 'forgot_password.php';</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: grey;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('images/image.jpg');
            background-size: cover;
            background-position: center;
        }

        .forgot-container {
            background-color: rgba(128, 128, 128, 0.8);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: pink;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            color: pink;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        .button-container {
            text-align: center;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: orange;
        }
    </style>
</head>

<body>

    <div class="forgot-container">
        <h2>Forgot Password</h2>
        <form action="forgot_password.php" method="POST">
            <div class="input-group">
                <label for="username">Enter your username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-group">
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="button-container">
                <button type="submit" class="btn">Verify</button>
            </div>
        </form>
    </div>

</body>

</html>
