<?php
require_once 'conn.php';
session_start();
if(isset($_SESSION['user_id']))
{
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Validate name
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter your name.";
        } else {
            $name = htmlspecialchars(trim($_POST["name"]));
        }

        // Validate email
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter your email.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            $email = htmlspecialchars(trim($_POST["email"]));
        }

        // Validate message
        if (empty(trim($_POST["message"]))) {
            $message_err = "Please enter your message.";
        } else {
            $message = htmlspecialchars(trim($_POST["message"]));
        }

        // Insert into database if no errors
        if (empty($name_err) && empty($email_err) && empty($message_err)) 
        {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact (name, email, message,user_id) VALUES (:name, :email, :message,:id)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':message', $message);
                $stmt->bindParam(':id', $_SESSION['user_id']);

                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "thank you";
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
                        'neon-yellow': '0 0 10px rgba(223, 255, 0, 0.5)'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-hacker-bg text-hacker-green min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-black/90 border border-gray-800 rounded-xl shadow-neon-green transition-all duration-300 hover:shadow-neon-yellow p-8">
        <h2 class="text-4xl font-bold text-hacker-yellow text-center mb-8 tracking-wider transform transition-all duration-300 hover:scale-105">Contact Us</h2>
        <form method="POST" >
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
</body>
</html>