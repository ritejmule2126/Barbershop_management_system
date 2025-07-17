<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; // Ensure this file exists

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user bookings from database
$sql = "SELECT id, service, appointment_date, status FROM bookings WHERE user_id = ? ORDER BY appointment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: #FFB6C1;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
            transition: 0.3s;
        }

        .navbar a:hover {
            color: #ff9900;
        }

        /* Table Styling */
        .booking-history {
            margin: 40px auto;
            width: 80%;
            background-color: #C0C0C0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: black;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Button Styling */
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: black;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .back-button:hover {
            background-color: #ff9900;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="dashboard.php">Home</a>
    <a href="?logout=true">Logout</a>
</div>

<h2>Booking History</h2>

<div class="booking-history">
    <table>
        <tr>
            <th>Name</th>
            <th>Service</th>
            <th>Barber</th>
            <th>Date & Time</th>
            <th>Status</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['service']); ?></td>
                    <td><?php echo htmlspecialchars($row['barber']); ?></td>
                    <td><?php echo htmlspecialchars($row['datetime']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No past bookings found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<a href="dashboard.php" class="back-button">Back to Dashboard</a>

</body>
</html>
