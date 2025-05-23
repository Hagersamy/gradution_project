<?php
session_start();
ini_set('display_errors', '0');
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conn.php';

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = htmlspecialchars( $_POST['old_password']) ?? '';
    $new_password = htmlspecialchars( $_POST['new_password']) ?? '';
    $confirm_password = htmlspecialchars($_POST['confirm_password']) ?? '';

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        try {
            // Fetch the current hashed password from the database
            $query = $pdo->prepare("SELECT password FROM users WHERE id = :id");
            $query->execute([':id' => $_SESSION['user_id']]);
            $user = $query->fetch();

            if ($user && password_verify($old_password, $user['password'])) {
                // Update password in the database
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_query = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                $update_query->execute([':password' => $hashed_password, ':id' => $_SESSION['user_id']]);

                $success_message = "Your password has been changed successfully.";
            } else {
                $error_message = "Old password is incorrect.";
            }
        } catch (PDOException $e) {
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
                    <p class="text-gray-300 mb-6">You are not authorized to access this resource. Please log in or contact system administration.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="/project/login.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            Go to Login
                        </a>
                        <a href="/project/contact.php" class="bg-gray-700 text-white px-6 py-2 rounded hover:bg-gray-600 transition">
                            Contact Support
                        </a>
                    </div>
                </div>
            </body>
            </html>');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
<body class="bg-dark text-light min-h-screen flex flex-col items-center justify-center">
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 w-full max-w-md">
        <h1 class="text-2xl text-secondary mb-4 text-center">Change Password</h1>

        <?php if (isset($error_message)): ?>
            <p class="bg-red-500 text-white px-4 py-2 rounded mb-4 text-center"> <?= htmlspecialchars($error_message); ?> </p>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <p class="bg-green-500 text-white px-4 py-2 rounded mb-4 text-center"> <?= htmlspecialchars($success_message); ?> </p>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="old_password" class="block mb-2">Old Password</label>
                <input type="password" id="old_password" name="old_password" class="w-full px-4 py-2 rounded bg-dark border border-slate-600 text-light focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label for="new_password" class="block mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 rounded bg-dark border border-slate-600 text-light focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label for="confirm_password" class="block mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 rounded bg-dark border border-slate-600 text-light focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <button type="submit" class="w-full bg-primary text-dark px-4 py-2 rounded hover:bg-secondary transition duration-300 ease-in-out">
                Change Password
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="userdashboard.php" class="text-secondary hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
