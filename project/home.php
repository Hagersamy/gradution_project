<?php
session_start();
require_once 'conn.php';

// Handle Search Query
$searchQuery = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
}

// Fetch Labs
$labs_query = $pdo->prepare(
    "SELECT * FROM labs WHERE approved = 1 AND labname LIKE :search"
);
$labs_query->execute([':search' => '%' . $searchQuery . '%']);
$labs = $labs_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PenTest Labs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-green-400 min-h-screen flex flex-col">
    <!-- Navigation Bar -->
    <nav class="bg-black/50 backdrop-blur-md px-6 py-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold text-green-500">PenTest Labs</div>
            <ul class="flex space-x-6">
                <?php 
                    if(isset($_SESSION['user_id']))
                    {
                        echo "<li><a href=userdashboard.php class=hover:text-yellow-400 transition>Dashboard</a></li>";
                    }
                    else
                    {
                        echo "<li><a href=login.php class=hover:text-yellow-400 transition>Login</a></li>";
                    }

                ?>
                <li><a href="usershowlabs.php" class="hover:text-yellow-400 transition">Labs</a></li>
                <li><a href="contact.php" class="hover:text-yellow-400 transition">Contact</a></li>
                <?php 
                    if(isset($_SESSION['user_id']))
                    {
                        echo "<li><a href=logout.php class=hover:text-yellow-400 transition>Logout</a></li>";
                    }
                ?>
            </ul>
        </div>
    </nav>

    <!-- Search Bar -->
    <div class="container mx-auto px-4 py-6">
        <form method="GET" action="home.php" class="max-w-xl mx-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search Labs and More" 
                    value="<?php echo htmlspecialchars($searchQuery); ?>"
                    class="w-full px-4 py-3 bg-gray-800 border border-green-600 rounded-lg text-green-400 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                <button 
                    type="submit" 
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-green-600 text-black px-4 py-2 rounded hover:bg-green-500 transition"
                >
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Hero Section -->
    <div class="relative flex-grow">
        <div class="absolute inset-0 overflow-hidden">
            <video 
                autoplay 
                muted 
                loop 
                class="w-full h-full object-cover opacity-30"
            >
                <source src="https://videos.pexels.com/video-files/4389357/4389357-uhd_3840_2024_30fps.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        
        <div class="relative container mx-auto px-4 py-24 text-center">
            <h1 class="text-5xl font-bold mb-4 text-white">Welcome to PenTest Labs</h1>
            <p class="text-xl mb-8 text-green-300">Practice your skills and solve cybersecurity labs</p>
            <a href="usershowlabs.php" class="inline-block bg-yellow-400 text-black px-8 py-3 rounded-lg hover:bg-yellow-500 transition">
                Explore Labs
            </a>
        </div>
    </div>

    <!-- Labs Section -->
    <section id="labs" class="bg-gray-800 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 text-yellow-400">Featured Labs</h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                <?php if (count($labs) > 0): ?>
                    <?php foreach ($labs as $lab): ?>
                        <div class="bg-gray-900 border border-green-800 rounded-lg p-6 transform transition hover:scale-105 hover:shadow-lg">
                            <h3 class="text-2xl font-semibold mb-4 text-yellow-400">
                                <?php echo htmlspecialchars($lab['labname']); ?>
                            </h3>
                            <p class="text-gray-300 mb-6">
                                <?php echo htmlspecialchars($lab['description'] ?? 'No description available.'); ?>
                            </p>
                            <a 
                                href="simulate_attacks.php?labid=<?php echo $lab['lab_id']; ?>" 
                                class="block w-full text-center bg-green-600 text-black py-3 rounded-lg hover:bg-green-500 transition"
                            >
                                Access Lab
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center text-gray-400">
                        <p>No labs found matching your search criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-6">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; 2024 PenTest Labs. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>