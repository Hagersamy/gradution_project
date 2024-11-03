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
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #000;
            color: #00ff00;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000;
            padding: 1rem 2rem;
        }

        .navbar .logo {
            font-size: 1.5rem;
            color: white;
        }

        .navbar .nav-links {
            list-style: none;
            display: flex;
        }

        .navbar .nav-links li {
            margin-left: 2rem;
        }

        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .navbar .nav-links a:hover {
            color: yellow;
        }

        /* Dashboard Main Content */
        .dashboard {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
        }

        .dashboard h1 {
            text-align: center;
            color: #ddff00;
            margin-bottom: 1.5rem;
        }

        /* User Info Section */
        .user-info {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid #1d1d1a;
            border-radius: 5px;
            background-color: #111;
        }

        .user-info h2 {
            color: #D3FF00;
        }

        .user-info p {
            margin-bottom: 0.5rem;
        }

        /* Labs Section */
        .labs {
            margin-bottom: 2rem;
        }

        .labs h2 {
            color: #D3FF00;
            margin-bottom: 1rem;
        }

        .lab-item {
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #111;
            border: 1px solid #1d1d1a;
            border-radius: 5px;
        }

        .lab-item h3 {
            color: #aeff00;
        }

        .labs .show-all-labs {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: yellow;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .labs .show-all-labs:hover {
            background-color: #aeff00;
        }

        /* View Lab Button */
        .view-lab-button {
            margin-top: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #00ff00;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .view-lab-button:hover {
            background-color: #aeff00;
        }

        /* Score Circle */
        
            .score {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2rem;
        }

        .score h2 {
            color: #D3FF00;
            margin-bottom: 1rem;
        }

        .score-circle {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #111;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            color: #D3FF00;
        }

        .score-circle .score-value {
            position: absolute;
        }

        /* Circle Animation */
        svg {
            transform: rotate(-90deg);
        }

        svg circle {
            fill: none;
            stroke-width: 8;
            stroke: yellow;
            stroke-dasharray: 440; /* Circumference of the circle */
            transition: stroke-dashoffset 1s ease;
        }


        /* Footer */
        .footer {
            background-color: #000;
            padding: 1rem;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
    <?php
      // Fetch user data from session
      $user_id = $_SESSION['user_id'];
      $user_query = $pdo->prepare("SELECT username, email, role FROM users WHERE id = :id");
      $user_query->execute([':id' => $user_id]);
      $user_data = $user_query->fetch(PDO::FETCH_ASSOC);
  
      // Fetch a few labs data to display on the dashboard
      $labs_query = $pdo->prepare("SELECT * FROM labs LIMIT 3");
      $labs_query->execute();
      $labs = $labs_query->fetchAll(PDO::FETCH_ASSOC);
  
      // Fetch solved labs with a JOIN to get the lab name and count
      $solvedlabs_query = $pdo->prepare("
          SELECT COUNT(sl.lab_id) AS solved_count, l.labname 
          FROM user_labs sl 
          JOIN labs l ON sl.lab_id = l.lab_id 
          WHERE sl.user_id = :id
          GROUP BY sl.user_id
      ");
      $solvedlabs_query->execute([':id' => $user_id]);
      $solved_data = $solvedlabs_query->fetch(PDO::FETCH_ASSOC);
      $solved_count = $solved_data['solved_count'] ?? 0;
  
      // Fetch list of solved labs for display
      $solved_labs_names_query = $pdo->prepare("
          SELECT l.labname 
          FROM user_labs sl
          JOIN labs l ON sl.lab_id = l.lab_id
          WHERE sl.user_id = :id
      ");
      $solved_labs_names_query->execute([':id' => $user_id]);
      $solved_labs = $solved_labs_names_query->fetchAll(PDO::FETCH_ASSOC);
  
      // Fetch user score
      $score_query = $pdo->prepare("SELECT score FROM users WHERE id = :id");
      $score_query->execute([':id' => $user_id]);
      $score_data = $score_query->fetch(PDO::FETCH_ASSOC);
      $score = $score_data ? $score_data['score'] : 0; // default score if not found
  
    ?>

     <!-- Navigation Bar -->
     <nav class="navbar">
        <div class="logo">Android Pentest Academy</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="labs.php">Labs</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="logout.php">Logout</a></li>

        </ul>
    </nav>

    <!-- Dashboard Main Content -->
    <div class="dashboard">
        <h1>User Dashboard</h1>

        <!-- User Info Section -->
        <section class="user-info">
            <h2>Your Information</h2>
            <p><strong>Username:</strong> <?= htmlspecialchars($user_data['username']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']); ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user_data['role']); ?></p>
        </section>
        <section class="score">
            <h2>Score</h2>
            <div class="score-circle">
                <svg width="150" height="150">
                    <circle cx="75" cy="75" r="70" style="--score: <?= htmlspecialchars($score); ?>"></circle>
                </svg>
                <div class="score-value"><?= htmlspecialchars($score); ?>%</div>
            </div>
        </section>

        <!-- Labs Section -->
        <section class="labs-section">
            <h2>Available Labs</h2>
            <?php foreach ($labs as $lab): ?>
                <div class="lab-item">
                    <p><strong>Lab Name:</strong> <?= htmlspecialchars($lab['labname']); ?></p>
                    <p><strong>Severity:</strong> <?= htmlspecialchars($lab['severity']); ?></p>
                    <p><strong>Score:</strong> <?= htmlspecialchars($lab['Lab_score']); ?></p>
                    <?php if (!empty($lab['description'])): ?>
                        <p><strong>Description:</strong> <?= htmlspecialchars($lab['description']); ?></p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($lab['laburl']); ?>" target="_blank" class="view-lab-button">View Lab</a>
                </div>
            <?php endforeach; ?>
            <center>
                <a href="labs.php" target="_blank" class="view-lab-button"> Show All Labs</a>
            </center>
        </section>

        <section class="solved-labs-section">
            <h2>Solved Labs</h2>
            <p><strong>Total Solved Labs:</strong> <?= htmlspecialchars($solved_count); ?></p>
            <ul>
                <?php foreach ($solved_labs as $solved_lab): ?>
                    <li class="solved-lab-item"><?= htmlspecialchars($solved_lab['labname']); ?></li>
                    <li class="solved-lab-item"><?= htmlspecialchars($solved_lab['laburl']); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <!-- Score Section -->
      
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Android Pentest Academy. All Rights Reserved.</p>
    </footer>

</body>
</html>