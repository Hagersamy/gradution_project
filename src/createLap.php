<?php
session_start();
require 'conn.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];

// Fetch the permissions for the logged-in role
$sql = "SELECT * FROM functionality_for_roles WHERE role = :role";
$stmt = $pdo->prepare($sql);
$stmt->execute(['role' => $role]); 
$permissions = $stmt->fetch(PDO::FETCH_ASSOC);

// Check permissions for creating labs
if (!$permissions['create_lab']) {
    die("You do not have permission to create labs.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
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
                <p class="text-gray-300 mb-6">INVALID CSRF TOKEN</p>
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

    $labName = htmlspecialchars($_POST['labname'], ENT_QUOTES, 'UTF-8');
    $severity = htmlspecialchars($_POST['severity'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
    $flag = htmlspecialchars($_POST['labflag']);
    if (strlen($flag) > 100) {
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
                    <p class="text-gray-300 mb-6">lab flag must be in 64 chars</p>
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
    $labScore = intval($_POST['labscore']);
    if ($labScore < 0) {
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
                <p class="text-gray-300 mb-6">score can not be negative.</p>
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

    // Validate file upload
    $targetDir = "uploads/";
    $uploadedFile = $_FILES['labfile']['name'];
    $fileExtension = pathinfo($uploadedFile, PATHINFO_EXTENSION);

    // Ensure the upload is an APK file
    if ($fileExtension !== 'apk') {
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
                <p class="text-gray-300 mb-6">Only .APK allowed</p>
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

    // Rename and move the uploaded file
    $uniqueFileName = uniqid() . '.apk';
    $targetFile = $targetDir . $uniqueFileName;

    if (!move_uploaded_file($_FILES['labfile']['tmp_name'], $targetFile)) {
        die("There was an error uploading the file.");
        exit();
    }
    
    $approved = 0; // Default approved status
    $creatorId = $_SESSION['user_id']; // Assuming the logged-in user is the creator
    
    try {
        $stmt = $pdo->prepare("INSERT INTO labs (labname, severity, description, laburl, Lab_score, approved, creator_id, flag) 
                               VALUES (:name, :severity, :description, :laburl, :Lab_score, :approved, :creator_id, :flag)");
        
        $stmt->execute([
            'name' => $labName,
            'severity' => $severity,
            'description' => $description,
            'laburl' => $targetFile,
            'Lab_score' => $labScore,
            'approved' => $approved,
            'creator_id' => $creatorId,
            'flag' => $flag
        ]);
        header("Location: showlabs.php");
    } catch (PDOException $e) {
        // Error handling with detailed error message
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-200">

    <!-- Navigation Bar -->
    <nav class="bg-black text-white py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <div class="text-xl font-bold text-cyan-500">Android Pentest Academy</div>
            <ul class="flex space-x-4">
                <li><a href="home.php" class="hover:text-cyan-400">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="userdashboard.php" class="hover:text-cyan-400">Dashboard</a></li>
                    <?php if ($_SESSION['role'] !== 'Support'): ?>
                        <li><a href="contact.php" class="hover:text-cyan-400">Contact</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="hover:text-cyan-400">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="hover:text-cyan-400">Login</a></li>
               <li><a href="assistant.php" class="hover:text-secondary transition duration-300 ease-in-out">AI BOT</a></li>
        
                    <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md bg-gray-800 shadow-2xl rounded-lg p-8 border border-gray-700">
            <h1 class="text-3xl font-bold text-cyan-400 mb-6 text-center">Create a New Lab</h1>
            
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div>
                    <label for="labname" class="block text-sm font-medium text-gray-300 mb-1">Lab Name</label>
                    <input 
                        type="text" 
                        id="labname" 
                        name="labname" 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                </div>

                <div>
                    <label for="labscore" class="block text-sm font-medium text-gray-300 mb-1">Lab Score</label>
                    <input 
                        type="number" 
                        id="labscore" 
                        name="labscore"
                        min=0 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                </div>

                <div>
                    <label for="labflag" class="block text-sm font-medium text-gray-300 mb-1">Lab Flag</label>
                    <input 
                        type="text" 
                        id="labflag" 
                        name="labflag" 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                </div>

                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-300 mb-1">Severity</label>
                    <select 
                        id="severity" 
                        name="severity" 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                        <option value="Low" class="bg-green-900 text-green-300">Low</option>
                        <option value="Medium" class="bg-yellow-900 text-yellow-300">Medium</option>
                        <option value="High" class="bg-orange-900 text-orange-300">High</option>
                        <option value="Critical" class="bg-red-900 text-red-300">Critical</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5" 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    ></textarea>
                </div>

                <div>
                    <label for="labfile" class="block text-sm font-medium text-gray-300 mb-1">Upload Lab (.apk file)</label>
                    <input 
                        type="file" 
                        id="labfile" 
                        name="labfile" 
                        accept=".apk" 
                        required 
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 text-gray-200 rounded-md file:mr-4 file:rounded-md file:border-0 file:bg-cyan-600 file:text-white file:px-4 file:py-2 hover:file:bg-cyan-700"
                    >
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-cyan-600 text-white py-2 rounded-md hover:bg-cyan-700 transition duration-300 font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50"
                    >
                        Submit Lab
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
