<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connect.php'; // Ensure this file exists

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Handle delete request & reorder IDs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Delete the customer
    $conn->query("DELETE FROM customer WHERE id = $id");

    // Reset ID sequence
    $conn->query("SET @count = 0;");
    $conn->query("UPDATE customer SET id = @count := @count + 1;");
    $conn->query("ALTER TABLE customer AUTO_INCREMENT = 1;");

    header("Location: manage_customers.php");
    exit();
}

// Fetch all customers
$result = $conn->query("SELECT * FROM customer ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
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
        }

        /* Container */
        .dashboard-container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 95%;
            max-width: 1100px;
            margin: 50px auto;
            box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }

        h1 {
            margin-bottom: 20px;
        }

        /* Table */
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

        /* Buttons */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: pink;
            color: black;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn:hover {
            background: rgba(0, 0, 0, 0.8);
            color: white;
        }

        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            background: red;
            color: white;
        }

        button:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .dashboard-container {
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

            .btn-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Manage Customers</h1>

        <!-- Buttons for navigation -->
        <div class="btn-container">
            <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
            <a href="add_customer.php" class="btn">Add Customer</a>
        </div>

        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                <?php 
                $new_id = 1; // Start numbering from 1
                while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $new_id++; ?></td> <!-- Display new ID -->
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
