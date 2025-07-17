<?php
session_start();
require 'db_connect.php'; // Ensure this file properly sets up `$conn`

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : 'N/A';

if ($booking_id > 0) {
    // Fetch appointment details from the database
    $query = "SELECT name, email, phone, service, barber, datetime, payment_method, notes, status, amount FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();
        $stmt->close();
    } else {
        die("Database query failed: " . $conn->error);
    }

    if (!$appointment) {
        die("Error: No appointment found for this ID.");
    }

    if ($payment_id !== 'N/A' && $appointment['status'] !== 'Paid') {
        // Update payment details if Razorpay was used
        $update_query = "UPDATE bookings SET payment_method = 'Razorpay', status = 'Paid' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        if ($update_stmt) {
            $update_stmt->bind_param("i", $booking_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
} else {
    die("Error: Invalid Booking ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Receipt - Barbershop</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; text-align: center; padding: 20px; }
        .receipt-container {
            max-width: 600px; margin: auto; background: white; padding: 20px; 
            border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        h2 { color: #333; }
        .details { text-align: left; margin-top: 20px; font-size: 18px; }
        .details p { margin: 10px 0; }
        .btn {
            padding: 10px 20px; background: #ff9900; color: white;
            text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;
        }
        .print-btn {
            padding: 10px 20px; background: #4CAF50; color: white;
            text-decoration: none; border-radius: 5px; cursor: pointer;
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <h2>Appointment Confirmed!</h2>
    <p>Thank you, <b><?= htmlspecialchars($appointment['name']) ?></b>. Your appointment has been successfully booked.</p>

    <h3>Appointment Receipt</h3>
    <div class="details">
        <p><strong>Payment ID:</strong> <?= htmlspecialchars($payment_id) ?></p>
        <p><strong>Service:</strong> <?= htmlspecialchars($appointment['service']) ?></p>
        <p><strong>Barber:</strong> <?= htmlspecialchars($appointment['barber']) ?></p>
        <p><strong>Appointment Date & Time:</strong> <?= htmlspecialchars($appointment['datetime']) ?></p>
        <p><strong>Amount Paid:</strong> ₹<?= number_format($appointment['amount'], 2) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($appointment['payment_method']) ?: 'Not Specified' ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($appointment['status']) ?></p>
        <p><strong>Notes:</strong> <?= htmlspecialchars($appointment['notes']) ?: 'N/A' ?></p>
    </div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>
    <br>
    <a href="booking.php" class="btn">Book Another Appointment</a>
</div>

</body>
</html>
