<?php 
session_start();
if(!isset($_GET["LabId"])&&$_SERVER['REQUEST_METHOD'] === 'GET')
{
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
            <p class="text-gray-300 mb-6">You are not authorized to access this resource.</p>
            <div class="flex justify-center space-x-4">
                <a href="/project/userdashboard.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                    Go to Dashboard
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
if(isset($_SESSION["user_id"]))
{
    require_once "conn.php";
    try
    {
        $sql="SELECT edit_lab,edit_self_lab FROM functionality_for_roles WHERE role=:rolee";
        $stat=$pdo->prepare($sql);
        $stat->bindParam(':rolee', $_SESSION['role']);
        $stat->execute();
        $approved=$stat->fetch(PDO::FETCH_ASSOC);
        if($approved&&($approved["edit_lab"]==1||$approved["edit_self_lab"]))
        {
            if($_SERVER['REQUEST_METHOD'] === 'GET') 
            {
                $_SESSION['LabId']=htmlspecialchars($_GET["LabId"]);
                if($_SESSION['role']==="Creator")
                {
                    $sql2="SELECT * From labs where lab_id = :Lid And creator_id=:cid";
                    $stat2=$pdo->prepare($sql2);
                    $stat2->bindParam(":Lid",$_SESSION['LabId'],PDO::PARAM_INT);
                    $stat2->bindParam(":cid",$_SESSION['user_id'],PDO::PARAM_INT);
                    $stat2->execute();
                    $lab=$stat2->fetch(PDO::FETCH_ASSOC);
                }
                elseif($_SESSION['role']==="Admin")
                {
                    $sql2="SELECT * From labs where lab_id = :Lid";
                    $stat2=$pdo->prepare($sql2);
                    $stat2->bindParam(":Lid",$_SESSION['LabId'],PDO::PARAM_INT);
                    $stat2->execute();
                    $lab=$stat2->fetch(PDO::FETCH_ASSOC);
                }
                
                if(empty($lab))
                {
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
                            <p class="text-gray-300 mb-6">You are not authorized to access this resource.</p>
                            <div class="flex justify-center space-x-4">
                                <a href="/project/userdashboard.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                                    Go to Dashboard
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') 
            {
                if (isset($_POST['action'])) 
                {
                if($_POST['action'] === 'Back')
                {
                    header('Location: showLabs.php');
                    exit();
                } 
                elseif($_POST['action'] === 'Edit')
                {
                    $name = htmlspecialchars(trim($_POST['name']));
                    $url = htmlspecialchars($_POST['url']);
                    $severity = htmlspecialchars(trim($_POST['severity']));
                    $flag = htmlspecialchars($_POST['flag']);
                    $score = intval($_POST['score']);
                    $description = htmlspecialchars(trim($_POST['LabDesc']));
                    $approve = intval($_POST['approve']);

                    $allowedSeverities = ['low', 'medium', 'high', 'critical'];
                    if (!in_array($severity, $allowedSeverities))
                    {
                        die("Invalid Lab Severity!");
                    }
                    if ($score < 0) 
                    {
                        die("Lab Score cannot be negative!");
                    }
                    if ($approve !== 0 && $approve !== 1) 
                    {
                        die("Approved value must be 0 or 1!");
                    }
                    try
                    {
                        if($_SESSION['role']==="Admin")
                        {
                            $sql = "UPDATE labs SET labname = :name, laburl = :url, severity = :severity, Lab_score = :score,
                            description = :description, flag =:flag , approved = :approve , admin_id =:aid  WHERE lab_id = :labId";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
                            $stmt->bindParam(':severity', $severity, PDO::PARAM_STR);
                            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                            $stmt->bindParam(':approve', $approve, PDO::PARAM_INT);
                            $stmt->bindParam(':aid', $_SESSION['user_id'], PDO::PARAM_INT);
                            $stmt->bindParam(':flag', $flag);
                            $stmt->bindParam(':labId', $_SESSION['LabId'], PDO::PARAM_INT);
                            $stmt->execute();
                            header("Location: showlabs.php");
                            exit;
                        }
                        else
                        {
                            $sql = "UPDATE labs SET labname = :name, laburl = :url, severity = :severity, Lab_score = :score,
                            description = :description, flag =:flag WHERE lab_id = :labId";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
                            $stmt->bindParam(':severity', $severity, PDO::PARAM_STR);
                            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                            $stmt->bindParam(':flag', $flag);
                            $stmt->bindParam(':labId', $_SESSION['LabId'], PDO::PARAM_INT);
                            $stmt->execute();
                            header("Location: showlabs.php");
                            exit;
                        }
                    }
                    catch(PDOException $e)
                    {
                        echo"Invalid Input\n";
                    }
                }
        }
    }
        }
        else
        {
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
    catch(PDOException $e)
    {
        echo"ERROR!!!\n";
    }
}
else
{
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Android Pentest Academy - Edit Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-blob {
            position: fixed;
            z-index: -10;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(20,184,166,0.3), rgba(79,70,229,0.3)), 
                        radial-gradient(circle at 30% 80%, rgba(13,148,136,0.4) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(124,58,237,0.4) 0%, transparent 50%);
            background-size: cover;
            background-position: center;
            filter: blur(100px);
            opacity: 0.7;
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen ">
    <div class="bg-blob"></div>
    <nav class="bg-blue text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold text-white-500">Android Pentest Academy</div>
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
    <div class="flex items-center justify-center p-4 min-h-[calc(100vh-64px)]">
    <div class="bg-gray-800/80 backdrop-blur-lg shadow-2xl rounded-lg w-full max-w-2xl p-8 border border-gray-700/50 relative z-10">
        <h1 class="text-2xl font-bold text-cyan-400 mb-6 text-center">Edit Lab Details</h1>
        <form method="POST" action="editLab.php" class="space-y-4">
            <?php if(isset($lab)){?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="LabName" class="block text-sm font-medium text-gray-300 mb-2">Lab Name:</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($lab['labname']); ?>" 
                               class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="Laburl" class="block text-sm font-medium text-gray-300 mb-2">Lab URL:</label>
                        <input type="text" name="url" value="<?php echo htmlspecialchars($lab['laburl']); ?>" 
                               class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="Labseverity" class="block text-sm font-medium text-gray-300 mb-2">Lab Severity:</label>
                        <select name="severity" class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="low" <?php echo $lab['severity'] == 'low' ? 'selected' : ''; ?> class="bg-green-900 text-green-300">Low</option>
                            <option value="medium" <?php echo $lab['severity'] == 'medium' ? 'selected' : ''; ?> class="bg-yellow-900 text-yellow-300">Medium</option>
                            <option value="high" <?php echo $lab['severity'] == 'high' ? 'selected' : ''; ?> class="bg-orange-900 text-orange-300">High</option>
                            <option value="critical" <?php echo $lab['severity'] == 'critical' ? 'selected' : ''; ?> class="bg-red-900 text-red-300">Critical</option>
                        </select>
                    </div>

                    <div>
                        <label for="LabScore" class="block text-sm font-medium text-gray-300 mb-2">Lab Score:</label>
                        <input type="number" name="score" min="0" value="<?php echo htmlspecialchars($lab['Lab_score']); ?>" 
                               class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <?php if($_SESSION['role']==="Admin"){?>
                    <div>
                        <label for="Labapproval" class="block text-sm font-medium text-gray-300 mb-2">Approved Value:</label>
                        <select name="approve" class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="0" <?php echo $lab['approved'] == 0 ? 'selected' : ''; ?>>0 (Not Approved)</option>
                            <option value="1" <?php echo $lab['approved'] == 1 ? 'selected' : ''; ?>>1 (Approved)</option>
                        </select>
                    </div>
                    <?php }?>
                    <div>
                           <label for="LabFlag" class="block text-sm font-medium text-gray-300 mb-2">Lab Flag:</label>
                           <input type="text" name="flag" value="<?php echo htmlspecialchars($lab['flag']); ?>" 
                            class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500">
                     </div>

                    <div class="col-span-full">
                        <label for="Labdesc" class="block text-sm font-medium text-gray-300 mb-2">Lab Description:</label>
                        <textarea name="LabDesc" rows="5" 
                                  class="w-full px-3 py-2 bg-gray-700/70 backdrop-blur-sm border border-gray-600/50 text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500"><?php echo htmlspecialchars($lab['description']); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="submit" name="action" value="Back" 
                            class="px-4 py-2 bg-cyan-600 text-gray-200 rounded-md hover:bg-cyan-700 transition-colors">
                        Back
                    </button>
                    <button type="submit" name="action" value="Edit" 
                            class="px-4 py-2 bg-cyan-600 text-white rounded-md hover:bg-cyan-700 transition-colors">
                        Edit
                    </button>
                </div>
            <?php }?>
        </form>
    </div>
    </div>
</body>
</html>