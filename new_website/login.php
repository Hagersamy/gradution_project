<?php
session_start();

require_once 'conn.php';

try {
    // Create a PDO instance
  

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize user input
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        // Check if the inputs are filled
        if (!$email || !$password) {
            $error = "Please fill in all required fields.";
        } else {
            // Prepare the SQL query to fetch user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Store user information in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['subs'] = $user['is_subscribed'];;
                
                // Redirect to the dashboard
                header("Location: userdashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
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
    <title>Android Pentest Academy - Login</title>
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
            background-color: #000;
            color: #00ff00;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000;
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
            color: yellow;
        }

        /* Hero Section for Login */
        .hero {
            position: relative;
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #df0;
            overflow: hidden;
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

        .hero .content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 1s;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* Login Form Section */
        .login-form {
            background-color: #1a1a1a;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            margin: auto;
        }

        .login-form input {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #111;
            border: 1px solid #1d1d1a;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .login-form input:focus {
            border-color: #aeff00;
        }

        .login-form button {
            width: 100%;
            padding: 15px;
            background-color: yellow;
            color: black;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #aeff00;
        }

        /* Error Message */
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Additional Links */
        .additional-links {
            margin-top: 20px;
            text-align: center;
        }

        .additional-links a {
            display: block;
            color: #D3FF00;
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
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Hero Section with Login Form -->
    <section class="hero">
        <video autoplay muted loop>
            <source src="https://videos.pexels.com/video-files/4389357/4389357-uhd_3840_2024_30fps.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="content">
            <h1>Login to Your Account</h1>
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form class="login-form" method="POST" action="">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit">Login</button>
            </form>
            <div class="additional-links">
                <a href="forgotpassword.php">Forgot Password?</a>
                <a href="register.php">New here? Sign up!</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>

</body>
</html>
