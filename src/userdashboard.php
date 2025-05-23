<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Pentest Academy - User Dashboard</title>
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
    <?php
      // Fetch user data from session
      $user_id = $_SESSION['user_id'];
      $user_query = $pdo->prepare("SELECT username, email, role FROM users WHERE id = :id");
      $user_query->execute([':id' => $user_id]);
      $user_data = $user_query->fetch(PDO::FETCH_ASSOC);
  
      // Fetch a few labs data to display on the dashboard
      $labs_query = $pdo->prepare("SELECT * FROM labs where approved=1  LIMIT 3");
      $labs_query->execute();
      $labs = $labs_query->fetchAll(PDO::FETCH_ASSOC);
  
      // Fetch user score
      $score_query = $pdo->prepare("SELECT score FROM users WHERE id = :id");
      $score_query->execute([':id' => $user_id]);
      $score_data = $score_query->fetch(PDO::FETCH_ASSOC);
      $score = $score_data ? $score_data['score'] : 0;
  
      $solvedlabs_query = $pdo->prepare("SELECT COUNT(*) AS solved_count FROM lab_attempts WHERE user_id = :id AND is_solved = 1");
      $solvedlabs_query->execute([':id' => $user_id]);
      $solved_data = $solvedlabs_query->fetch(PDO::FETCH_ASSOC);
      $solved_count = $solved_data['solved_count'] ?? 0;
    ?>

    <!-- Navigation Bar -->
    <nav class="bg-dark text-light px-8 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-primary">Android Pentest Academy</div>
        <ul class="flex space-x-6">
            <li><a href="home.php" class="hover:text-secondary transition duration-300 ease-in-out">Home</a></li>
            <?php
            if($_SESSION['role']=="Creator" ||$_SESSION['role']=="Admin" )
            {
                echo "<li><a href='createLap.php' class='hover:text-secondary transition duration-300 ease-in-out'>Create Labs</a></li>";
            }
            if($_SESSION['role']=="Admin")
            {
                echo "<li><a href='user_managment.php' class='hover:text-secondary transition duration-300 ease-in-out'>users</a></li>";
            }
            if($_SESSION['role']=="Support")
            {
                echo "<li><a href='resolve_ticket.php' class='hover:text-secondary transition duration-300 ease-in-out'>Tickets</a></li>";
            }
            if($_SESSION['role']!="Hacker")
            {
                echo "<li><a href='showlabs.php' class='hover:text-secondary transition duration-300 ease-in-out'>Labs</a></li>";
            }
            else
            {
                echo "<li><a href='usershowlabs.php' class='hover:text-secondary transition duration-300 ease-in-out'>Labs</a></li>";
            }
            if($_SESSION['role']!="Support")
            {
                echo "<li><a href='contact.php' class='hover:text-secondary transition duration-300 ease-in-out'>Contact</a></li>";
            }
            ?>
            <li><a href='leaderboard.php' class='hover:text-secondary transition duration-300 ease-in-out'>Leaderboard</a></li>
            <li><a href="assistant.php" class='hover:text-secondary transition duration-300 ease-in-out'>AI BOT</a></li>
            <li><a href="logout.php" class='hover:text-secondary transition duration-300 ease-in-out'>Logout</a></li>
        </ul>
    </nav>

    <!-- Dashboard Main Content -->
    <div class="container mx-auto px-4 py-8 flex-grow">
        <h1 class="text-4xl text-center text-primary mb-8">User Dashboard</h1>
        <div class="grid md:grid-cols-2 gap-8">
            <!-- User Info Section -->
            <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 hover:scale-105 transition duration-300 ease-in-out">
                <h2 class="text-2xl text-secondary mb-4">Your Information</h2>
                <?php if($user_data){?>
                <p class="mb-2"><strong>Username:</strong> <?= htmlspecialchars($user_data['username']); ?></p>
                <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($user_data['email']); ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($user_data['role']); ?></p>
                <?php
                     }
                     else {
                        // Unset all session variables
                        $_SESSION = array();
                        // Destroy the session
                        session_destroy();
                        header("Location:login.php");
                     }
                ?>
                 <!-- Reset Password Button -->
                <div class="mt-4">
                    <a href="changepassword.php" 
                       class="bg-secondary text-dark px-4 py-2 rounded hover:bg-primary transition duration-300 ease-in-out">
                        Change Password
                    </a>
                </div>
            </div>


            <!-- Score Section -->
            <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 hover:scale-105 transition duration-300 ease-in-out">
                <h2 class="text-2xl text-secondary mb-4">Score</h2>
                <div class="flex justify-center items-center">
                    <div class="relative w-48 h-48 rounded-full bg-dark border-4 border-primary flex items-center justify-center">
                        <div class="text-4xl text-secondary"><?= htmlspecialchars($score); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Labs Section -->
        <div class="mt-8 bg-slate-800 border border-slate-700 rounded-lg p-6">
            <h2 class="text-2xl text-secondary mb-6">Available Labs</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($labs as $lab): ?>
                    <div class="bg-dark border border-slate-700 rounded-lg p-4 hover:scale-105 transition duration-300 ease-in-out">
                        <p class="text-lg font-semibold text-primary mb-2"><?= htmlspecialchars($lab['labname']); ?></p>
                        <p class="mb-1"><strong>Severity:</strong> <?= htmlspecialchars($lab['severity']); ?></p>
                        <p class="mb-1"><strong>Score:</strong> <?= htmlspecialchars($lab['Lab_score']); ?></p>
                        <?php if (!empty($lab['description'])): ?>
                            <p class="mb-4"><strong>Description:</strong> <?= htmlspecialchars($lab['description']); ?></p>
                        <?php endif; ?>
                        <a href="simulate_attacks.php?labid=<?= $lab['lab_id']; ?>" 
                           class="inline-block bg-primary text-dark px-4 py-2 rounded hover:bg-secondary transition duration-300 ease-in-out">
                            Access Lab
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-6">
                <a href="usershowlabs.php" class="bg-secondary text-dark px-6 py-3 rounded hover:bg-primary transition duration-300 ease-in-out">
                    Show All Labs
                </a>
            </div>
        </div>

        <!-- Solved Labs Section -->
        <div class="mt-8 bg-slate-800 border border-slate-700 rounded-lg p-6 hover:scale-105 transition duration-300 ease-in-out">
            <h2 class="text-2xl text-secondary mb-4">Solved Labs</h2>
            <p class="text-lg"><strong>Total Solved Labs:</strong> <?= htmlspecialchars($solved_count); ?></p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-4">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>
</body>
</html>