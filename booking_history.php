<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php'; // Include database connection file

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

// Get logged-in user's email to fetch their bookings
$user_email = $_SESSION['user']['email'];

// Check if a delete request was made
if (isset($_GET['delete_booking_id'])) {
    $delete_booking_id = $_GET['delete_booking_id'];

    // Ensure the booking ID is valid and not empty
    if (!empty($delete_booking_id) && is_numeric($delete_booking_id)) {
        // Delete the booking from the database
        $delete_sql = "DELETE FROM bookings WHERE booking_id = ? AND email = ?";
        $delete_stmt = $conn->prepare($delete_sql);

        if ($delete_stmt === false) {
            // Prepare failed, handle error
            die('MySQL prepare error: ' . $conn->error);
        }

        $delete_stmt->bind_param("is", $delete_booking_id, $user_email);

        if ($delete_stmt->execute()) {
            echo "<script>alert('Booking cancelled and deleted successfully');</script>";
            // After successful deletion, redirect to prevent re-submission
            echo "<script>window.location.href = 'booking_history.php';</script>";
            exit();
        } else {
            // Handle failure
            echo "<script>alert('Failed to cancel and delete booking. Please try again.');</script>";
        }
    } else {
        // If the booking ID is invalid
        echo "<script>alert('Invalid booking ID');</script>";
    }
}

// Fetch bookings from the database for the logged-in user
$sql = "SELECT * FROM bookings WHERE email = ? ORDER BY datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
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
        /* Your existing styles */
    </style>
    <script>
        // Function to confirm cancellation before proceeding
        function confirmCancellation(bookingId) {
            var confirmation = confirm("Are you sure you want to cancel and delete this booking?");
            if (confirmation) {
                window.location.href = "?delete_booking_id=" + bookingId; // Redirect with delete_booking_id
            }
        }
    </script>
</head>
<body>

<h2>Your Booking History</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Service</th>
            <th>Barber</th>
            <th>Date & Time</th>
            <th>Payment</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['service']) ?></td>
                <td><?= htmlspecialchars($row['barber']) ?></td>
                <td><?= date("d M Y, h:i A", strtotime($row['datetime'])) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td>$<?= htmlspecialchars($row['amount']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if ($row['status'] !== 'Cancelled'): ?>
                        <button onclick="confirmCancellation(<?= $row['booking_id'] ?>)" class="cancel-btn">Cancel</button>
                    <?php else: ?>
                        <span>Cancelled</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No bookings found.</p>
<?php endif; ?>

<a href="dashboard.php" class="back-btn">Back to Dashboard</a>

</body>
</html>
