<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Android Pentest Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#10b981', // Emerald green
                        'secondary': '#22d3ee', // Cyan
                        'dark': '#0f172a', // Slate dark
                        'light': '#f0fdf4', // Light green-white
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-light min-h-screen flex flex-col">
    
    <!-- Navigation Bar -->
    <nav class="bg-dark text-light px-8 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-primary">Android Pentest Academy</div>
        <ul class="flex space-x-6">
            <li><a href="home.php" class="hover:text-secondary transition duration-300 ease-in-out">Home</a></li>
            <li><a href='usershowlabs.php' class='hover:text-secondary transition duration-300 ease-in-out'>Labs</a></li>
            <li><a href='contact.php' class='hover:text-secondary transition duration-300 ease-in-out'>Contact</a></li>
            <li><a href="logout.php" class="hover:text-secondary transition duration-300 ease-in-out">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 flex-grow">
        <h1 class="text-4xl text-center text-primary mb-8">Password Reset</h1>
        
        <!-- Password Reset Form -->
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 max-w-md mx-auto hover:scale-105 transition duration-300 ease-in-out">
            <h2 class="text-2xl text-secondary mb-4 text-center">Enter your Email to Reset Password</h2>
            <form action="" method="POST">
                <label for="email" class="block text-lg mb-2">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" 
                    class="w-full p-2 border border-slate-700 rounded bg-dark text-light mb-4">

                <button type="submit" 
                    class="w-full bg-secondary text-dark px-4 py-2 rounded hover:bg-primary transition duration-300 ease-in-out">
                    Send Reset Link
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-4">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>
</body>
</html>';
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
include('conn.php');

function generateToken($length = 64) {
    return bin2hex(random_bytes($length));
}

function sendResetEmail($userEmail, $resetToken) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';             
        $mail->SMTPAuth   = true;
        $mail->Username   = 'put your email'; 
        $mail->Password   = 'put your password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('put your email', 'Android Pentest Academy');
        $mail->addAddress($userEmail);
        $mail->addReplyTo('put your email', 'Android Pentest Academy');

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = 'Click the link below to reset your password: <br><br>'
                       . '<a href="http://localhost/project/reset_password.php?token=' . $resetToken . '">Reset Password</a>';
        $mail->AltBody = 'To reset your password, please visit the following link: '
                       . 'http://localhost/project/reset_password.php?token=' . $resetToken;

        $mail->send();
        echo 'Password reset email sent successfully!';
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['email'])) {
    $email = htmlspecialchars($_POST['email']);  

    $stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $updateStmt = $pdo->prepare('UPDATE users SET resetpass = :resetpass, resetpass_expires = :expires WHERE email = :email');
        $updateStmt->execute([
            'resetpass' => $token,
            'expires'   => $expires,
            'email'     => $email
        ]);

        sendResetEmail($email, $token);
    } else {
        echo 'Email not found!';
    }
}
?>
