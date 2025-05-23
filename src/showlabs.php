<?php 
session_start();
if(isset($_SESSION["user_id"]))
{
    if($_SESSION['role']=="Hacker")
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
    require_once "conn.php";
    try
    {
        $sql1="SELECT * from functionality_for_roles where role=:rolee";
        $stat1=$pdo->prepare($sql1);
        $stat1->bindParam(':rolee',$_SESSION['role']);
        $stat1->execute();
        $access=$stat1->fetch(PDO::FETCH_ASSOC);

        $sql2="SELECT * From labs ORDER BY lab_id DESC";
        $stat2=$pdo->prepare($sql2);
        $stat2->execute();
        $labs=$stat2->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <!DOCTYPE html>
        <html lang="en" class="dark">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Labs Management</title>
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
            <div class="container mx-auto px-4 py-8 max-w-7xl">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                        <i class="fas fa-user-secret mr-3 text-blue-500"></i>
                        Labs Management
                    </h1>
                    <?php if($access['create_lab']==1): ?>
                        <a href="createLap.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Create New Lab
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if($labs): ?>
                <div class="overflow-x-auto shadow-2xl rounded-lg">
                    <table class="w-full bg-gray-800 rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-700 text-gray-300 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 flex text-left">
                                    <i class="fas fa-hashtag mr-2"></i>Lab ID
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-tag mr-2"></i>Lab Name
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-link mr-2"></i>Lab URL
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-chart-line mr-2"></i>Severity
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-star mr-2"></i>Score
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-info-circle mr-2"></i>Description
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-tag mr-2"></i>Flag
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-check-circle mr-2"></i>Approved
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-user-circle mr-2"></i>Creator
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <i class="fas fa-crown mr-2"></i>Admin
                                </th>
                                <th class="px-4 py-3 text-center">
                                    <i class="fas fa-cogs mr-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
    <?php foreach($labs as $lab): ?>
        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors duration-200">
            <?php foreach($lab as $key => $value): ?>
                <td class="px-4 py-3 
                    <?php 
                        if($key == 'lab_name') echo 'truncate max-w-[10rem]';
                        elseif($key == 'description') echo 'truncate max-w-[14rem]';
                        elseif($key == 'flag') echo 'truncate max-w-[8rem]';
                    ?>" 
                    title="<?php echo htmlspecialchars($value); ?>">
                    
                    <?php 
                    if($key == "laburl") {
                        echo "<a href='" . htmlspecialchars($value) . "' target='_blank' class='text-blue-400 hover:text-blue-300 transition-colors flex items-center'>
                            <i class='fas fa-external-link-alt mr-2'></i>Lab Link
                        </a>";
                    } else {
                        echo htmlspecialchars($value);
                    }
                    ?>
                </td>
            <?php endforeach; ?>

            <td class="px-4 py-3 flex text-center space-x-2">
                <?php if($access['edit_lab']==1 || $_SESSION["user_id"] === $lab['creator_id']): ?>
                    <a href="editLab.php?LabId=<?php echo htmlspecialchars($lab['lab_id']); ?>" 
                       class="inline-block bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-500 transition-colors duration-200 text-sm mb-1 flex items-center justify-center">
                        <i class="fas fa-edit mr-1"></i>
                    </a>
                <?php endif; ?>
                
                <?php if($access['delete_labs']==1): ?>
                    <a href="deleteLab.php?LabId=<?php echo htmlspecialchars($lab['lab_id']); ?>" 
                       class="inline-block bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-500 transition-colors duration-200 text-sm flex items-center justify-center">
                        <i class="fas fa-trash-alt mr-1"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
 
                </div>
                <?php else: ?>
                    <div class="text-center bg-gray-800 p-8 rounded-lg shadow-2xl">
                        <i class="fas fa-folder-open text-6xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400 text-xl">No labs found!</p>
                    </div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }
    catch(PDOException $e)
    {
        echo "ERROR!!!\n";
    }
}
else
{
    header("Location: login.php");
    exit();
}
?>