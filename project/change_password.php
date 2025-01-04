<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conn.php';

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error_message = "Both fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Update password in the database
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_query = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $update_query->execute([':password' => $hashed_password, ':id' => $_SESSION['user_id']]);

        $success_message = "Your password has been changed successfully .";
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
                <label for="new_password" class="block mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 rounded bg-dark border border-slate-600 text-light focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label for="confirm_password" class="block mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 rounded bg-dark border border-slate-600 text-light focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <button type="submit" class="w-full bg-primary text-dark px-4 py-2 rounded hover:bg-secondary transition duration-300 ease-in-out">
                Reset Password
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="userdashboard.php" class="text-secondary hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
