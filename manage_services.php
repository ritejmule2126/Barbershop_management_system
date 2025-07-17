<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Handle Add Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $category = trim($_POST['category']);
    $service_name = trim($_POST['service_name']);
    $price = floatval($_POST['price']);

    if (!empty($category) && !empty($service_name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO services (category, service_name, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $category, $service_name, $price);
        if ($stmt->execute()) {
            echo "<script>alert('Service added successfully!'); window.location.href='manage_services.php';</script>";
        } else {
            echo "<script>alert('Error adding service.');</script>";
        }
        $stmt->close();
    }
}

// Handle Delete Service
if (isset($_POST['delete_service'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Service deleted successfully!'); window.location.href='manage_services.php';</script>";
    }
    $stmt->close();
}

// Handle Update Service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_service'])) {
    $id = intval($_POST['id']);
    $category = trim($_POST['category']);
    $service_name = trim($_POST['service_name']);
    $price = floatval($_POST['price']);

    if (!empty($category) && !empty($service_name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE services SET category = ?, service_name = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $category, $service_name, $price, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Service updated successfully!'); window.location.href='manage_services.php';</script>";
        } else {
            echo "<script>alert('Error updating service.');</script>";
        }
        $stmt->close();
    }
}

// Fetch all services
$result = $conn->query("SELECT * FROM services ORDER BY id ASC");

// Fetch single service for editing (if needed)
$service_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $service_to_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: url('images/image.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: white;
        }

        .container {
            width: 95%;
            max-width: 800px;
            margin: auto;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .input-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        label {
            color: white;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            background: white;
            color: black;
            outline: none;
        }

        button {
            background: pink;
            color: black;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background: grey;
            color: white;
        }

        .delete-btn {
            background: red;
            color: white;
            padding: 10px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background: darkred;
        }

        .back-btn {
            display: block;
            width: fit-content;
            background: pink;
            color: black;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px auto;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: grey;
            color: white;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            color: black;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid black;
            text-align: center;
            font-size: 16px;
        }

        th {
            background: pink;
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style><style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background: url('images/image.jpg') no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 20px;
    }

    h1 {
        color: white;
    }

    .container {
        width: 95%;
        max-width: 800px;
        margin: auto;
        background: rgba(0, 0, 0, 0.8);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .input-group {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }

    label {
        color: white;
        font-weight: bold;
        margin-bottom: 5px;
    }

    input {
        width: 100%;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 16px;
        background: white;
        color: black;
        outline: none;
        box-sizing: border-box; /* Ensures consistent sizing */
    }

    button {
        background: pink;
        color: black;
        padding: 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        margin-top: 10px;
        transition: 0.3s;
    }

    button:hover {
        background: grey;
        color: white;
    }

/* Edit Button Style */
.edit-btn {
    background: blue;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
}

.edit-btn:hover {
    background: darkblue;
    color: white;
}


    /* Make inputs full width on smaller screens */
    @media (max-width: 600px) {
        .container {
            width: 100%;
            padding: 10px;
        }

        input {
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
    <div class="container">
        <h1>Manage Services</h1>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>

        <?php if ($service_to_edit): ?>
            <h2>Edit Service</h2>
            <form method="POST">
                <div class="input-group">
                    <label for="category">Category:</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($service_to_edit['category']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="service_name">Service Name:</label>
                    <input type="text" name="service_name" value="<?php echo htmlspecialchars($service_to_edit['service_name']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="price">Price:</label>
                    <input type="number" name="price" value="<?php echo htmlspecialchars($service_to_edit['price']); ?>" step="1" required>
                </div>
                <input type="hidden" name="id" value="<?php echo $service_to_edit['id']; ?>">
                <button type="submit" name="update_service">Update Service</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <div class="input-group">
                   <label for="category">Category:</label>
                   <input type="text" name="category" placeholder="Enter Category" required>
                </div>
                <div class="input-group">
                   <label for="service_name">Service Name:</label>
                   <input type="text" name="service_name" placeholder="Enter Service Name" required>
                </div>
                <div class="input-group">
                   <label for="price">Price:</label>
                   <input type="number" name="price" placeholder="Enter Price" step="1" required>
                </div>
                <button type="submit" name="add_service">Add Service</button>
            </form>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Service Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td>₹<?php echo number_format($row['price'], 2); ?>
                            <a href="manage_services.php?edit_id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
</td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_service" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
