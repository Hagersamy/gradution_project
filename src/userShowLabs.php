<?php 
session_start();
if(!isset($_SESSION["user_id"])) {
    $_SESSION["guest"] = true; 
}
require_once "conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Pentest Academy - Labs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary-green': '#10B981',
                        'dark-bg': '#111827',
                        'dark-card': '#1F2937'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg text-white min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold">Android Pentest Academy</div>
            <ul class="flex space-x-4">
                <li><a href="home.php" class="hover:text-primary-green">Home</a></li>            
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="userdashboard.php" class="hover:text-primary-green">Dashboard</a></li>
                    <li><a href='leaderboard.php' class='hover:text-yellow-400 transition'>Leaderboard</a></li>
                    <li><a href="assistant.php" class="hover:text-primary-green">AI BOT</a></li>
                    <li><a href="contact.php" class="hover:text-primary-green">Contact</a></li>
                    <li><a href="logout.php" class="hover:text-primary-green">Logout</a></li>
                <?php endif; ?>       
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <h1 class="text-3xl text-center text-primary-green mb-8">Available Labs</h1>

        <?php 
        try {
            $sql2 = "SELECT * FROM labs WHERE approved = 1 ORDER BY lab_id DESC";
            $stat2 = $pdo->prepare($sql2);
            $stat2->execute();
            $labs = $stat2->fetchAll(PDO::FETCH_ASSOC);
            if ($labs) {
                echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';
                foreach ($labs as $lab) {
                    echo '
                    <div class="bg-dark-card p-6 rounded-lg shadow-lg overflow-hidden min-h-[250px] flex flex-col justify-between">
                        <div>
                            <h2 class="text-xl text-primary-green mb-4">' . htmlspecialchars($lab['labname']) . '</h2>
                            <div class="space-y-2 mb-4">
                                <p><strong>Severity:</strong> <span class="' . 
                                (($lab['severity'] == 'High') ? 'text-red-500' : 
                                 (($lab['severity'] == 'Medium') ? 'text-yellow-500' : 'text-green-500')) . 
                                '">' . htmlspecialchars($lab['severity']) . '</span></p>
                                <p><strong>Lab Score:</strong> ' . htmlspecialchars($lab['Lab_score']) . '</p>
                                <p class="text-sm">' . 
                                htmlspecialchars(substr($lab['description'], 0, 20)) . 
                                (strlen($lab['description']) > 20 ? '...' : '') . '</p>
                            </div>
                        </div>
                        <a href="simulate_attacks.php?labid=' . $lab['lab_id'] . '" 
                           class="w-full block text-center bg-primary-green text-white py-2 rounded hover:bg-green-600 transition duration-300">
                            Access Lab
                        </a>
                    </div>';
                }
                echo '</div>';
            } else {
                echo '<div class="text-center text-yellow-500 text-xl">No Labs Available</div>';
            }
        } catch(PDOException $e) {
            echo '<div class="text-center text-red-500 text-xl">Error Loading Labs</div>';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-black w-full text-white py-4 text-center mt-8">
        <p>&copy; 2025 Android Pentest Academy. All Rights Reserved.</p>
    </footer>
</body>
</html>
