<?php
session_start();

// Database configuration
require_once 'conn.php';

try {
    // Create a PDO instance
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];
        $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);

        // Validate required fields
        if (!$username || !$email || !$password || !$age) {
            $error = "Please fill in all required fields.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, age) 
                                   VALUES (:username, :email, :password, :age)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':age', $age);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error during registration. Please try again.";
            }
        }
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Pentest Academy - Register</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #000; /* Black background */
            color: #00ff00; /* Green text color */
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000; /* Black background */
            padding: 1rem 2rem;
        }

        .navbar .logo {
            font-size: 1.5rem;
            color: white;
        }

        .navbar .nav-links {
            list-style: none;
            display: flex;
        }

        .navbar .nav-links li {
            margin-left: 2rem;
        }

        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .navbar .nav-links a:hover {
            color: yellow; /* Change to yellow on hover */
        }

        /* Hero Section for Registration */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            text-align: center;
        }

        .hero video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: 1;
            transform: translate(-50%, -50%);
            object-fit: cover;
            opacity: 0.5;
        }

        .hero h1 {
            position: relative;
            z-index: 2;
            font-size: 2rem;
            color: #ddff00; /* Green color for the heading */
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 1s;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        /* Registration Form */
        .registration-form {
            display: flex;
            flex-direction: column;
        }

        .registration-form input {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #111; /* Dark input background */
            border: 1px solid #1d1d1a;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .registration-form input:focus {
            border-color: #aeff00; /* Green focus border */
        }

        .registration-form button {
            width: 100%;
            padding: 15px;
            background-color: yellow; /* Yellow button */
            color: black;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .registration-form button:hover {
            background-color: #aeff00; /* Green button on hover */
        }

        /* Additional Links */
        .additional-links {
            margin-top: 20px;
        }

        .additional-links a {
            color: #D3FF00; /* Green link color */
            margin: 10px 0;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .additional-links a:hover {
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            background-color: #000;
            padding: 1rem;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">Android Pentest Academy</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
        </ul>
    </nav>

    <!-- Hero Section with Registration Form -->
    <section class="hero">
        <video autoplay muted loop>
            <source src="https://videos.pexels.com/video-files/4389357/4389357-uhd_3840_2024_30fps.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <!-- Heading outside the form -->
        <h1>Create Your Account</h1>
        
        <div class="content">
            <form class="registration-form" method="post">
                <input type="text" placeholder="Enter your username" required name="username">
                <input type="email" placeholder="Enter your email" required name="email">
                <input type="password" placeholder="Enter your password" required name="password">
                <input type="number" placeholder="Enter your age" required name="age">
                <button type="submit">Register</button>
            </form>
            <div class="additional-links">
                <a href="login.php">Already have an account? Login!</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>

</body>
</html>
