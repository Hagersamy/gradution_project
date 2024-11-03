<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Optionally, redirect to login page or home page
    header("Location: home.php"); // Change 'index.php' to your desired landing page after logout
    exit();
} else {
    // If no session exists, redirect to login page
    header("Location: home.php"); // Change 'index.php' to your desired landing page
    exit();
}
?>
