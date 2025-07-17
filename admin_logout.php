<?php
session_start();
session_destroy();
header("Location: admin_login.html"); // Redirect to login page
exit();
?>
