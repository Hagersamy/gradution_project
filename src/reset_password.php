<?php
// Include your database connection
include('conn.php');

// Function to reset password
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Android Pentest Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#10b981',
                        'secondary': '#22d3ee',
                        'dark': '#0f172a',
                        'light': '#f0fdf4',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-light min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-dark text-light px-8 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-primary">Android Pentest Academy</div>
        <ul class="flex space-x-6">
            <li><a href="home.php" class="hover:text-secondary transition">Home</a></li>
        </ul>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-10 flex-grow">
        <div class="max-w-md mx-auto bg-slate-800 p-6 rounded-lg shadow-md border border-slate-700">
            <h1 class="text-2xl font-bold text-center text-primary mb-4">Reset Your Password</h1>
            <?php
            if (isset($_GET['token'])) {
                $token = htmlspecialchars($_GET['token']);

                // Check if the token exists and is valid
                $stmt = $pdo->prepare('SELECT id, resetpass, resetpass_expires FROM users WHERE resetpass = :resetpass');
                $stmt->execute(['resetpass' => $token]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    if (new DateTime() > new DateTime($user['resetpass_expires'])) {
                        echo '<div class="text-red-500 mb-4">Token has expired. Please request a new password reset.</div>';
                    } else {
                        // Handle password reset
                        if (isset($_POST['reset_password'])) {
                            $newPassword = htmlspecialchars($_POST['new_password']);
                            $confirmPassword = htmlspecialchars($_POST['confirm_password']);

                            if ($newPassword === $confirmPassword) {
                                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                                $updateStmt = $pdo->prepare('UPDATE users SET password = :password, resetpass = NULL, resetpass_expires = NULL WHERE id = :id');
                                $updateStmt->execute([
                                    'password' => $hashedPassword,
                                    'id'       => $user['id']
                                ]);
                                echo '<div class="text-green-500 font-semibold text-center">Your password has been reset successfully!</div>';
                                header('Location: logout.php');
                                exit('<div class="text-green-500 font-semibold text-center">Your password has been reset successfully!</div>');
                            } else {
                                echo '<div class="text-red-500 mb-4">Passwords do not match!</div>';
                            }
                        }

                        // Show password reset form
                        echo '<form method="POST" action="" class="space-y-4">
                                <div>
                                    <label class="block mb-1">New Password:</label>
                                    <input type="password" name="new_password" required class="w-full p-2 rounded bg-dark border border-slate-600 text-light">
                                </div>
                                <div>
                                    <label class="block mb-1">Confirm Password:</label>
                                    <input type="password" name="confirm_password" required class="w-full p-2 rounded bg-dark border border-slate-600 text-light">
                                </div>
                                <button type="submit" name="reset_password" class="w-full bg-secondary text-dark px-4 py-2 rounded hover:bg-primary transition">Reset Password</button>
                              </form>';
                    }
                } else {
                    echo '<div class="text-red-500 mb-4">Invalid or expired token.</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-4 mt-10">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>
</body>
</html>
