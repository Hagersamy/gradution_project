<?php
session_start();
require_once 'conn.php';

// Fetch users ordered by score (assumes 'users' table has 'username' and 'score')
$query = $pdo->query("SELECT username, score FROM users ORDER BY score DESC LIMIT 10");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-green-400 min-h-screen flex flex-col">

<!-- Navigation -->
<nav class="bg-black/50 backdrop-blur-md px-6 py-4">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-2xl font-bold text-green-500">Android Pentest Academy</div>
        <ul class="flex space-x-6">
            <li><a href="home.php" class="hover:text-yellow-400 transition">Home</a></li>
            <li><a href="usershowlabs.php" class="hover:text-yellow-400 transition">Labs</a></li>
            <li><a href="leaderboard.php" class="hover:text-yellow-400 transition">Leaderboard</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="userdashboard.php" class="hover:text-yellow-400 transition">Dashboard</a></li>
                <li><a href="logout.php" class="hover:text-yellow-400 transition">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="hover:text-yellow-400 transition">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Leaderboard Table -->
<main class="container mx-auto px-4 py-10">
    <div class="flex items-center justify-center mb-10 space-x-4">
        <img src="https://img.icons8.com/emoji/48/000000/trophy-emoji.png" alt="Trophy" class="w-10 h-10">
        <h1 class="text-4xl font-bold text-yellow-400">Leaderboard</h1>
    </div>

    <div class="overflow-x-auto max-w-3xl mx-auto">
        <table class="w-full bg-gray-800 border border-green-600 rounded-lg">
            <thead>
                <tr class="bg-green-700 text-black">
                    <th class="py-3 px-6 text-left">Rank</th>
                    <th class="py-3 px-6 text-left">Username</th>
                    <th class="py-3 px-6 text-left">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): ?>
                    <tr class="<?php echo $index % 2 == 0 ? 'bg-gray-900' : 'bg-gray-800'; ?>">
                        <td class="py-3 px-6"><?php echo $index + 1; ?></td>
                        <td class="py-3 px-6"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="py-3 px-6"><?php echo $user['score']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                    <tr><td colspan="3" class="text-center py-6 text-gray-400">No users available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Footer -->
<footer class="bg-black py-6 mt-auto">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-400">&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
