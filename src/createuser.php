<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') 
{
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if ($username && $email && $password && $role) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
        ]);

        $_SESSION['success'] = 'User created successfully!';
        header('Location: user_managment.php');
        exit();
    } else {
        $error = 'Please fill out all fields correctly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold text-green-500">Android Pentest Academy</div>
            <ul class="flex space-x-4">
                <li><a href="home.php" class="hover:text-green-400 transition">Home</a></li>
                <?php 
                if (isset($_SESSION['user_id'])) {
                    echo '<li><a href="userdashboard.php" class="hover:text-green-400 transition">Dashboard</a></li>';
                    if ($_SESSION['role'] != "Support") {
                        echo '<li><a href="contact.php" class="hover:text-green-400 transition">Contact</a></li>';
                    }
                    echo '<li><a href="logout.php" class="hover:text-green-400 transition">Logout</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>

    <!-- Form Section -->
    <div class="flex items-center justify-center p-6">
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
            <h1 class="text-2xl font-bold text-green-500 mb-6">Create New User</h1>

            <?php if (!empty($error)): ?>
                <div class="bg-red-600 text-white px-4 py-2 mb-4 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium">Username</label>
                    <input type="text" name="username" id="username" class="w-full px-4 py-2 bg-gray-700 rounded text-gray-100" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 bg-gray-700 rounded text-gray-100" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2 bg-gray-700 rounded text-gray-100" required>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium">Role</label>
                    <select name="role" id="role" class="w-full px-4 py-2 bg-gray-700 rounded text-gray-100" required>
                        <option value="Admin">Admin</option>
                        <option value="Creator">Creator</option>
                        <option value="Support">Support</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div class="flex justify-between">
                  
                    <a href="user_managment.php" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white">
                        Back
                    </a>
                   
                    <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
