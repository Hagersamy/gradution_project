<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
ini_set('display_errors', 0);

require_once 'conn.php'; 
require_once 'utils.php';

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $id = $_SESSION['user_id'];
    $_SESSION["guest"] = false;
}

if(!isset($_SESSION["user_id"])) {
    $_SESSION["guest"] = true; 
}

if (isset($_GET['labid'])) {
    $labid = intval($_GET['labid']);
    $permissions = getPermissions($pdo, $_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-gray-100 min-h-screen">

<nav class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold text-green-500">Android Pentest Academy</div>
            <ul class="flex space-x-4">
            <li><a href="home.php" class="hover:text-green-400 transition">Home</a></li>
               <li><a href="assistant.php" class="hover:text-secondary transition duration-300 ease-in-out">AI BOT</a></li>
         <?php 
                   if(isset($_SESSION['user_id']))
                    {
                       echo' <li><a href="userdashboard.php" class="hover:text-green-400 transition">Dashboard</a></li>';
                       if($_SESSION['role']!="Support")
                       {
                            echo' <li><a href="contact.php" class="hover:text-green-400 transition">Contact</a></li>';
                       }
                       echo'  <li><a href="logout.php" class="hover:text-green-400 transition">Logout</a></li>';
                    }
                ?>      
                </ul>
            </div>
        </nav>


    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <?php
        $sql2 = "SELECT * FROM labs WHERE lab_id = :id AND approved = 1";
        $stat2 = $pdo->prepare($sql2);
        $stat2->bindParam(':id', $labid, PDO::PARAM_INT);
        $stat2->execute();
        $labs = $stat2->fetchAll(PDO::FETCH_ASSOC);

        if ($labs) {
            foreach ($labs as $lab) {
        ?>
        <div class="bg-gray-800 rounded-lg shadow-2xl p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-blue-400 flex items-center">
                <i class="fas fa-user-secret mr-3 text-blue-500"></i>
                    <?php echo htmlspecialchars($lab['labname']); ?>
                </h1>
                <div class="flex items-center space-x-2">
                    <span class="bg-<?php echo $lab['severity'] === 'High' ? 'red' : ($lab['severity'] === 'Medium' ? 'yellow' : 'green'); ?>-600 px-3 py-1 rounded-full text-sm">
                        <?php echo htmlspecialchars($lab['severity']); ?> Severity
                    </span>
                    <span class="bg-blue-600 px-3 py-1 rounded-full text-sm">
                        Score: <?php echo htmlspecialchars($lab['Lab_score']); ?>
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-300 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Description
                </h2>
                <p class="text-gray-400"><?php echo htmlspecialchars($lab['description']); ?></p>
            </div>

            <?php if ($_SESSION["guest"] == true): ?>
                <div class="text-center">
                    <a href="login.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition duration-300 flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login To Access Lab
                    </a>
                </div>
            <?php elseif (isset($_SESSION['user_id']) && $permissions['simulate_attacks'] === 1): ?>
                <div class="flex justify-between items-center">
                    <a href="<?php echo htmlspecialchars($lab['laburl']); ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-300 flex items-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Access Lab
                    </a>

                    <div class="flex-grow ml-4">
                        <form method="POST" class="flex space-x-2">
                            <input type="text" name="flag" required placeholder="Enter flag" 
                                   class="flex-grow bg-gray-700 border border-gray-600 text-gray-100 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center">
                                <i class="fas fa-flag mr-2"></i>
                                Submit Flag
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php
            }
        } else {
            echo '<div class="text-center bg-gray-800 p-8 rounded-lg shadow-2xl">';
            echo '<i class="fas fa-folder-open text-6xl text-gray-600 mb-4"></i>';
            echo '<p class="text-gray-400 text-xl">No Labs To Show!!</p>';
            echo '</div>';
        }

        // Handle flag submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flag']) && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $submitted_flag = htmlspecialchars($_POST['flag']);
            
            $sql = "SELECT * from lab_attempts where lab_id=:lid and user_id=:uid";
            $sql1 = $pdo->prepare($sql);
            $sql1->bindParam(':lid', $labid, PDO::PARAM_INT);
            $sql1->bindParam(':uid', $id, PDO::PARAM_INT);
            $sql1->execute();
            $is_solved = $sql1->fetchAll(PDO::FETCH_ASSOC);
            
            if($is_solved) {
                foreach ($is_solved as $sol) {
                    if($sol['is_solved'] == 1) {
                        $sql_flag = "SELECT flag FROM labs WHERE lab_id = :id";
                        $stmt_flag = $pdo->prepare($sql_flag);
                        $stmt_flag->bindParam(':id', $labid, PDO::PARAM_INT);
                        $stmt_flag->execute();
                        $correct_flag = $stmt_flag->fetchColumn();
                        
                        if ($correct_flag && $submitted_flag === $correct_flag) {
                            echo '<div class="bg-green-600 text-white px-4 py-3 rounded-lg text-center mb-4">';
                            echo 'Flag submitted successfully! Your score has been updated before.';
                            echo '</div>';
                            exit;
                        }
                    }
                }
            }

            // Retrieve the correct flag for the lab
            $sql_flag = "SELECT flag FROM labs WHERE lab_id = :id";
            $stmt_flag = $pdo->prepare($sql_flag);
            $stmt_flag->bindParam(':id', $labid, PDO::PARAM_INT);
            $stmt_flag->execute();
            $correct_flag = $stmt_flag->fetchColumn();

            if ($correct_flag && $submitted_flag === $correct_flag) {
                // Correct flag submitted
                $update_lab_attempts = "INSERT INTO lab_attempts (user_id, lab_id, failed_attempts, is_solved) 
                                       VALUES (:user_id, :lab_id, 0, 1) 
                                       ON DUPLICATE KEY UPDATE is_solved = 1, failed_attempts = 0";
                $stmt_update_lab = $pdo->prepare($update_lab_attempts);
                $stmt_update_lab->execute(['user_id' => $user_id, 'lab_id' => $labid]);

                // Update user score
                $update_user_score = "UPDATE users SET score = score + :lab_score WHERE id = :user_id";
                $stmt_update_score = $pdo->prepare($update_user_score);
                $stmt_update_score->execute(['lab_score' => $lab['Lab_score'], 'user_id' => $user_id]);

                echo '<div class="bg-green-600 text-white px-4 py-3 rounded-lg text-center mb-4">';
                echo 'Flag submitted successfully! Your score has been updated.';
                echo '</div>';
            } else {
                // Incorrect flag submitted
                $update_failed_attempts = "INSERT INTO lab_attempts (user_id, lab_id, failed_attempts, is_solved) 
                                          VALUES (:user_id, :lab_id, 1, 0) 
                                          ON DUPLICATE KEY UPDATE failed_attempts = failed_attempts + 1";
                $stmt_failed_attempts = $pdo->prepare($update_failed_attempts);
                $stmt_failed_attempts->execute(['user_id' => $user_id, 'lab_id' => $labid]);

                echo '<div class="bg-red-600 text-white px-4 py-3 rounded-lg text-center mb-4">';
                echo 'Incorrect flag! Please try again.';
                echo '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
<?php
}
?>