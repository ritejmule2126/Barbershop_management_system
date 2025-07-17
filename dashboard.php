<?php
session_start();
include 'db_connect.php'; // Include database connection file

// Handle logout process
if (isset($_GET['logout'])) {
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: index.html"); // Redirect to the login page
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

// Fetch services from the database
$services = [];
$sql = "SELECT * FROM services ORDER BY category";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[$row['category']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbershop Dashboard</title>
    <style>
        /* Body Styling */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: black;
            color: #FFB6C1;
            background-size: 400% 400%;
            animation: gradientAnimation 10s ease infinite;
            padding-top: 70px;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navbar Styling */
        .navbar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);
            width: 100%;
        }

        .navbar.sticky {
            position: fixed;
            top: 0;
        }

        .navbar h1 {
            color: #fff;
            font-size: 24px;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            padding-right: 50px;
        }

        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        .navbar ul li {
            display: inline;
        }

        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .navbar ul li a:hover {
            background-color: pink;
            transform: scale(1.05);
        }

        /* Hero Section */
        .hero {
            background-image: url('images/img.jpg');
            background-size: cover;
            background-position: center;
            height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }

        .hero h2 {
            font-size: 48px;
            margin: 0;
            color: black;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
        }

        .hero p {
            font-size: 18px;
            margin-top: 10px;
            color: black;
        }

        /* Section Styling */
        .section {
            padding: 40px 20px;
            text-align: center;
            opacity: 1;
            transition: opacity 1s ease;
        }

        .section.blink {
            animation: blinkAnimation 1s ease forwards;
        }

        @keyframes blinkAnimation {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }

        .section h3 {
            font-size: 32px;
            margin-bottom: 20px;
            color: black;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }

        .section p {
            font-size: 18px;
            margin-bottom: 20px;
            color: #eee;
        }

        .services {
            padding: 40px;
            color: black;
            background: #C0C0C0;
            max-width: 800px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }
        .service {
            margin-bottom: 20px;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        .dropdown {
            display: none;
            margin-top: 10px;
        }
        .dropdown .subservice {
            display: flex;
            justify-content: space-between;
            padding: 5px;
            background: #eee;
            border-radius: 3px;
            margin-top: 5px;
        }
        .book-btn {
            background-color: #222;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .booking {
            background-color: #C0C0C0;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        .booking a {
            text-decoration: none;
            background-color: #222;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s, transform 0.2s ease;
        }

        .booking a:hover {
            background-color: pink;
            transform: scale(1.1);
        }

        /* Contact Us Section */
        .contact-us {
            padding: 40px 20px;
            background-color: #C0C0C0;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            margin: 40px 0;
        }

        .contact-us h3 {
            font-size: 32px;
            margin-bottom: 20px;
            color: black;
        }

        .contact-us p {
            font-size: 18px;
            margin-bottom: 20px;
            color: #555;
        }

        .contact-us .social-icons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .contact-us .social-icons a img {
            width: 35px;
            height: 35px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .contact-us .social-icons a img:hover {
            opacity: 1;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .navbar ul {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .hero h2 {
                font-size: 36px;
            }

            .section h3 {
                font-size: 28px;
            }

            .services .card, .team .card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .services .card img, .team .card img {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px 20px;
            }

            .navbar h1 {
                font-size: 18px;
            }

            .hero h2 {
                font-size: 28px;
            }

            .section h3 {
                font-size: 24px;
            }

            .services .card img, .team .card img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar" id="navbar">
    <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#booking">Book Appointment</a></li>
<li><a href="booking_history.php" target="_self">Booking History</a></li>
        <li><a href="#contact-us">Contact Us</a></li>
        <!-- Modified logout button to work with PHP -->
        <li><a href="?logout=true">Logout</a></li> 
    </ul>
    <h1>Barbershop</h1>
</div>

<!-- Hero Section -->
<div class="hero">
    <div>
        <h2>Looking Your Best Never <br>Goes Out of Style</h2>
    </div>
</div>

<div id="services" class="services">
    <h3>Our Services</h3>
    <?php foreach ($services as $category => $items): ?>
        <div class="service" onclick="toggleDropdown('<?= strtolower(str_replace(' ', '-', $category)) ?>')">
            <h4><?= htmlspecialchars($category) ?></h4>
            <div id="<?= strtolower(str_replace(' ', '-', $category)) ?>" class="dropdown">
                <?php foreach ($items as $item): ?>
                    <div class="subservice">
                        <span><?= htmlspecialchars($item['service_name']) ?> - $<?= htmlspecialchars($item['price']) ?></span>
                        <button class="book-btn" onclick="bookService('<?= htmlspecialchars($item['service_name']) ?>')">Book</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>



<!-- Booking Section -->
<div id="booking" class="section booking">
    <h3>Book Your Appointment</h3>
    <p>Reserve your spot today and enjoy the ultimate grooming experience.</p>
    <a href="booking.php">Book Now</a>
</div>

<!-- Contact Us Section -->
<div id="contact-us" class="contact-us">
    <h3>Contact Us</h3>
    <p>Feel free to get in touch with us on our social media or visit our location for more information.</p>
    <div class="social-icons">
        <a href="http://www.facebook.com" target="_blank" rel="nofollow">
            <img src="images/facebook.png" alt="Facebook">
        </a>
        <a href="http://instagram.com" target="_blank" rel="nofollow">
            <img src="images/instagram.png" alt="Instagram">
        </a>
        <a href="https://twitter.com" target="_blank" rel="nofollow">
            <img src="images/twitter.png" alt="Twitter">
        </a>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Barbershop. All rights reserved.</p>
</footer>

<!-- Sticky Navbar JavaScript -->
<script>
    window.onscroll = function() {stickyNavbar()};

    var navbar = document.getElementById("navbar");
    var sticky = navbar.offsetTop;

    function stickyNavbar() {
        if (window.pageYOffset >= sticky) {
            navbar.classList.add("sticky");
        } else {
            navbar.classList.remove("sticky");
        }
    }

    // Function to add the blink effect to the clicked section
    document.querySelectorAll('.navbar ul li a').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            if (this.getAttribute('href') !== "?logout=true") {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);

                // Add blink effect
                targetSection.classList.add('blink');
                setTimeout(function() {
                    targetSection.classList.remove('blink');
                }, 1000);

                // Smooth scroll to the target section
                window.scrollTo({
                    top: targetSection.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
<script>
    function toggleDropdown(id) {
        let dropdown = document.getElementById(id);
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
    function bookService(service) {
        window.location.href = `booking.html?service=${encodeURIComponent(service)}`;
    }
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault();
            window.location.href = this.href;
        });
    });
});
</script>
</body>
</html>
