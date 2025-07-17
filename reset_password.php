<?php
session_start();
include 'db_connect.php';

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username from the URL
$username = isset($_GET['username']) ? $_GET['username'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST["new_password"], PASSWORD_BCRYPT);
    $username = $_POST["username"];

    // Update password in the database
    $stmt = $conn->prepare("UPDATE customer SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute()) {
        echo "<script>alert('Password updated successfully!'); window.location.href = 'index.html';</script>";
    } else {
        echo "<script>alert('Failed to update password.'); window.location.href = 'reset_password.php?username=" . urlencode($username) . "';</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .reset-container {
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

    <div class="reset-container">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">

            <div class="input-group">
                <label for="new_password">Enter new password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="button-container">
                <button type="submit" class="btn">Update Password</button>
            </div>
        </form>
    </div>

</body>

</html>
