<?php 
session_start();
if(isset($_SESSION["user_id"]))
{
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
            <div class="container mx-auto px-4 py-8 max-w-7xl">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                        <i class="fas fa-flask mr-3 text-blue-500"></i>
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
                                        <td class="px-4 py-3">
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
                    </table>
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