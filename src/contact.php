<?php
require_once 'conn.php';
session_start();
if(isset($_SESSION['user_id']))
{
    if($_SESSION['role']=="Support")
    {
        die('<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://cdn.tailwindcss.com"></script>
            <title>Access Denied</title>
        </head>
        <body class="bg-gray-900 flex items-center justify-center min-h-screen">
            <div class="bg-red-900/30 border-2 border-red-600 rounded-xl p-8 max-w-md w-full text-center">
                <div class="mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-red-500 mb-4">Unauthorized Access</h1>
                <p class="text-gray-300 mb-6">You are not authorized to access this resource.</p>
                <div class="flex justify-center space-x-4">
                    <a href="/project/userdashboard.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                        Go to Dashboard
                    </a>
                    
                </div>
            </div>
        </body>
        </html>');
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter your name.";
        } else {
            $name = htmlspecialchars(trim($_POST["name"]));
        }

        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            $email = htmlspecialchars(trim($_POST["email"]));
        }

        if (empty(trim($_POST["message"]))) {
            $message_err = "Please enter your message.";
        } else {
            $message = htmlspecialchars(trim($_POST["message"]));
        }

        if (empty($name_err) && empty($email_err) && empty($message_err)) 
        {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact (name, email, message, user_id) VALUES (:name, :email, :message, :id)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':message', $message);
                $stmt->bindParam(':id', $_SESSION['user_id']);

                if ($stmt->execute()) {
                    echo "Thank you!";
                    header("Location: home.php");
                    exit();
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
else
{
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Android Pentest Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hacker-bg': '#0a0a0a',
                        'hacker-green': '#39ff14',
                        'hacker-yellow': '#dfff00',
                    },
                    boxShadow: {
                        'neon-green': '0 0 10px rgba(57, 255, 20, 0.5)',
                        'neon-yellow': '0 0 10px rgba(223, 255, 0, 0.5)',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-hacker-bg text-hacker-green min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold text-green-500">Android Pentest Academy</div>
            <ul class="flex space-x-4">
                <li><a href="home.php" class="hover:text-yellow-400 transition">Home</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="userdashboard.php" class="hover:text-yellow-400 transition">Dashboard</a></li>
                    <li><a href="contact.php" class="hover:text-yellow-400 transition">Contact</a></li>
                    <li><a href="logout.php" class="hover:text-yellow-400 transition">Logout</a></li>
                    <li><a href="assistant.php" class="hover:text-secondary transition duration-300 ease-in-out">AI BOT</a></li>
        
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Contact Form -->
    <div class="flex items-center justify-center p-4 min-h-[calc(100vh-64px)]">
        <div class="w-full max-w-md bg-black/90 border border-gray-800 rounded-xl shadow-neon-green transition-all duration-300 hover:shadow-neon-yellow p-8">
            <h2 class="text-4xl font-bold text-hacker-yellow text-center mb-8 tracking-wider transform transition-all duration-300 hover:scale-105">Contact Us</h2>
            <form method="POST">
                <div class="mb-6 group">
                    <label for="name" class="block text-hacker-yellow mb-2 transition-colors duration-300 group-hover:text-white">Name</label>
                    <input 
                        type="text"
                        id="name"
                        name="name"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-hacker-green focus:outline-none focus:border-hacker-yellow focus:ring-2 focus:ring-hacker-green transition-all duration-300 hover:bg-gray-800"
                    >
                </div>
                
                <div class="mb-6 group">
                    <label for="email" class="block text-hacker-yellow mb-2 transition-colors duration-300 group-hover:text-white">Email</label>
                    <input 
                        type="email"
                        id="email"
                        name="email"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-hacker-green focus:outline-none focus:border-hacker-yellow focus:ring-2 focus:ring-hacker-green transition-all duration-300 hover:bg-gray-800"
                    >
                </div>
                
                <div class="mb-6 group">
                    <label for="message" class="block text-hacker-yellow mb-2 transition-colors duration-300 group-hover:text-white">Message</label>
                    <textarea 
                        id="message"
                        name="message"
                        rows="5"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-hacker-green focus:outline-none focus:border-hacker-yellow focus:ring-2 focus:ring-hacker-green transition-all duration-300 hover:bg-gray-800"
                    ></textarea>
                </div>
                
                <button 
                    type="submit"
                    class="w-full bg-hacker-yellow text-black py-3 rounded-lg font-semibold uppercase tracking-wider 
                           transition-all duration-300 transform hover:scale-105 hover:bg-white 
                           focus:outline-none focus:ring-2 focus:ring-hacker-green active:scale-95"
                >
                    Send Message
                </button>
            </form>
        </div>
    </div>
</body>
</html>
