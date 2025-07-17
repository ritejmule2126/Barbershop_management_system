<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; 

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Fetch total customers
$customer_result = $conn->query("SELECT COUNT(*) AS total_customers FROM customer");
$customer_count = $customer_result->fetch_assoc()['total_customers'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('images/image.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .dashboard-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.3);
            text-align: center;
        }

        h1 {
            color: white;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            width: 150px;
        }

        .card h2 {
            font-size: 18px;
            color: white;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
            color: pink;
        }

        .dashboard-links {
            margin-top: 20px;
        }

        .dashboard-links a {
            display: inline-block;
            background: pink;
            color: black;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px;
            transition: 0.3s;
        }

        .dashboard-links a:hover {
            background: grey;
            color: black;
        }

        @media (max-width: 600px) {
            .dashboard-container {
                width: 90%;
                padding: 15px;
            }

            .card {
                width: 120px;
                padding: 10px;
            }

            .dashboard-links a {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo $_SESSION['admin']; ?>!</h1>

        <div class="stats">
            <div class="card">
                <h2>Total Customers</h2>
                <p><?php echo $customer_count; ?></p>
            </div>
        </div>

        <div class="dashboard-links">
            <a href="manage_customers.php">Manage Customers</a>
            <a href="view_bookings.php">Manage Bookings</a>
            <a href="manage_services.php">Manage Services</a> 
            <a href="admin_logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
