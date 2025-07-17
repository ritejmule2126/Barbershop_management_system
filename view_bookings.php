<?php
session_start();
include 'db_connect.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Fetch all bookings
$result = $conn->query("SELECT * FROM bookings ORDER BY id ASC");

// Initialize message variable
$message = "";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $status = trim($_POST['status']);

    if (!empty($id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                $message = "✅Status updated successfully!";
            } else {
                $message = "Error updating status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing update statement: " . $conn->error;
        }
    } else {
        $message = "Error: Missing ID or Status.";
    }
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if (!empty($id)) {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                // Reset auto-increment
                $conn->query("ALTER TABLE bookings AUTO_INCREMENT = 1");
                $message = "✅Booking deleted successfully!";
            } else {
                $message = "Error deleting booking: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing delete statement: " . $conn->error;
        }
    } else {
        $message = "Error: Invalid Booking ID.";
    }
}

// Reload the page to reflect changes after displaying message
if (!empty($message)) {
    echo "<script>
        alert('$message');
        window.location.href = '".$_SERVER['PHP_SELF']."';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('images/img.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 95%;
            max-width: 1100px;
            margin: 50px auto;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            color: black;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid black;
            text-align: center;
        }
        th {
            background: pink;
            color: black;
        }
        form {
            display: inline-block;
        }
        select, input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            transition: 0.3s;
        }
        .paid-btn {
            background: green;
            color: white;
        }
        .delete-btn {
            background: red;
            color: white;
        }
        .dashboard-link {
            display: inline-block;
            background: pink;
            color: black;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px auto;
            transition: 0.3s;
        }
        .dashboard-link:hover {
            background: rgba(0, 0, 0, 0.8);
            color: white;
        }
        @media screen and (max-width: 768px) {
            .container {
                width: 100%;
                padding: 15px;
            }
            th, td {
                padding: 8px;
            }
            button {
                font-size: 14px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Bookings</h1>
        <a href="admin_dashboard.php" class="dashboard-link">Back to Dashboard</a>
        <div class="table-container">
            <table>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Barber</th>
                    <th>Date & Time</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php $count = 1; while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['service']); ?></td>
                        <td><?php echo htmlspecialchars($row['barber']); ?></td>
                        <td><?php echo htmlspecialchars($row['datetime']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']) ?: 'N/A'; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Paid" <?php echo ($row['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                                    <option value="Unpaid" <?php echo ($row['status'] == 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                                </select>
                                <button type="submit" name="update" class="paid-btn">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
