<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; // Ensure this file exists

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

$message = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (!empty($username) && !empty($email) && !empty($phone)) {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO customer (username, email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $phone);

        if ($stmt->execute()) {
            $message = "<p class='success-message'>Customer added successfully!</p>";
        } else {
            $message = "<p class='error-message'>Error adding customer.</p>";
        }

        $stmt->close();
    } else {
        $message = "<p class='error-message'>All fields are required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Customer</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background: url('images/image.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: rgba(0, 0, 0, 0.85);
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.3);
            text-align: left;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .input-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
        }

        input {
            width: calc(100% - 20px);
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            background: white;
            color: black;
            outline: none;
        }

        input:focus {
            border-color: pink;
            box-shadow: 0px 0px 5px pink;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: pink;
            color: black;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
        }

        button:hover {
            background: grey;
            color: white;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            background: pink;
            color: black;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: grey;
            color: white;
        }

        /* Success & Error Messages */
        .success-message, .error-message {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.2);
            color: green;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            color: red;
        }

        /* Responsive Design */
        @media screen and (max-width: 600px) {
            .form-container {
                width: 95%;
                padding: 20px;
            }

            input {
                width: calc(100% - 20px);
                font-size: 14px;
                padding: 10px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add New Customer</h1>

        <?php echo $message; ?>

        <form method="POST">
            <div class="input-group">
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="button-container">
                <button type="submit">Add Customer</button>
            </div>
        </form>

        <a href="manage_customers.php" class="back-btn">Back to Manage Customers</a>
    </div>
</body>
</html>
