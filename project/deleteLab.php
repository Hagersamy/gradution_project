<?php 
session_start();
if(isset($_SESSION["user_id"]))
{
    require_once "conn.php";
    try
    {
        $sql="SELECT delete_labs FROM functionality_for_roles WHERE role=:rolee";
        $stat=$pdo->prepare($sql);
        $stat->bindParam(':rolee', $_SESSION['role']);
        $stat->execute();
        $approved=$stat->fetch(PDO::FETCH_ASSOC);
        if($approved&&$approved["delete_labs"]==1)
        {
            if($_SERVER['REQUEST_METHOD'] === 'GET'&&isset($_GET["LabId"])) 
            {
                $_SESSION['LabId']=htmlspecialchars($_GET["LabId"]);
                $sql2="SELECT * From labs where lab_id = :Lid ";
                $stat2=$pdo->prepare($sql2);
                $stat2->bindParam(":Lid",$_GET["LabId"],PDO::PARAM_INT);
                $stat2->execute();
                $lab=$stat2->fetch(PDO::FETCH_ASSOC);
                $labDetails = $lab; // Store lab details for later use
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
                    elseif($_POST['action'] === 'Delete')
                    {
                        try
                        {
                            if(isset($_SESSION['LabId']))
                            {
                                $sql3="DELETE FROM labs WHERE lab_id= :Lid";
                                $stat3=$pdo->prepare($sql3);
                                $stat3->bindParam(":Lid",$_SESSION['LabId'],PDO::PARAM_INT);
                                $stat3->execute();
                                // Store success message in session to show after redirect
                                $_SESSION['delete_message'] = "Lab Deleted Successfully!";
                                header('Location: showLabs.php');
                                exit();
                            }
                            else
                            {
                                echo"Invalid Input\n";
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
    <title>Android Pentest Academy - Delete Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md bg-white shadow-md rounded-lg p-8">
        <div class="text-center mb-6">
            <svg class="mx-auto mb-4 h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-800">Confirm Deletion</h2>
            <p class="text-gray-600 mt-2">Are you sure you want to delete this lab?</p>
        </div>

        <?php if(isset($labDetails)): ?>
        <div class="bg-gray-50 p-4 rounded-md mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Lab Details</h3>
            <ul class="space-y-1 text-sm text-gray-600">
                <?php foreach($labDetails as $key => $value): ?>
                    <?php if($key !== 'laburl'): ?>
                        <li>
                            <span class="font-medium"><?= htmlspecialchars(ucfirst($key)) ?>:</span> 
                            <?= htmlspecialchars($value) ?>
                        </li>
                    <?php else: ?>
                        <li>
                            <span class="font-medium">Lab URL:</span> 
                            <a href="<?= htmlspecialchars($value) ?>" class="text-blue-600 hover:underline" target="_blank">
                                <?= htmlspecialchars($value) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="deleteLab.php" class="flex justify-between space-x-4">
            <button 
                type="submit" 
                name="action" 
                value="Back" 
                class="flex-1 bg-gray-200 text-gray-800 py-2 rounded-md hover:bg-gray-300 transition duration-300"
            >
                Cancel
            </button>
            <button 
                type="submit" 
                name="action" 
                value="Delete" 
                class="flex-1 bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition duration-300"
            >
                Confirm Delete
            </button>
        </form>
    </div>
</body>
</html>