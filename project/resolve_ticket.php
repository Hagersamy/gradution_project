<?php 
session_start();
if(isset($_SESSION["user_id"]))
{
    require_once "conn.php";
    try
    {
        $sql="SELECT resolve_tickets FROM functionality_for_roles WHERE role=:rolee";
        $stat=$pdo->prepare($sql);
        $stat->bindParam(':rolee', $_SESSION['role']);
        $stat->execute();
        $approved=$stat->fetch(PDO::FETCH_ASSOC);
        if($approved && $approved["resolve_tickets"] == 1)
        {
            $sql2 = "SELECT contact.id, contact.name, contact.email, contact.message, contact.created_at, contact.isSolved, contact.user_id, users.username
                     FROM contact 
                     INNER JOIN users ON contact.user_id = users.id";
            $stat2 = $pdo->prepare($sql2);
            $stat2->execute();
            $problems = $stat2->fetchAll(PDO::FETCH_ASSOC);
            
            ?>
            <!DOCTYPE html>
            <html lang="en" class="dark">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Support Tickets</title>
                <script src="https://cdn.tailwindcss.com"></script>
                <script>
                    tailwind.config = {
                        darkMode: 'class',
                    }
                </script>
            </head>
            <body class="bg-gray-900 text-gray-100 min-h-screen">
                <div class="container mx-auto px-4 py-8">
                    <h1 class="text-3xl font-bold mb-6 text-center text-gray-100">Support Tickets</h1>
                    
                    <?php if($problems): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full bg-gray-800 shadow-md rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-700 text-gray-300">
                                    <th class="px-4 py-3 text-left">Problem ID</th>
                                    <th class="px-4 py-3 text-left">User Name</th>
                                    <th class="px-4 py-3 text-left">User Email</th>
                                    <th class="px-4 py-3 text-left">Problem</th>
                                    <th class="px-4 py-3 text-left">Submitted At</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($problems as $problem): ?>
                                    <?php 
                                    $mailto_link = "mailto:{$problem['email']}?subject=" . urlencode("Regarding your problem: {$problem['id']}") . "&body=" . urlencode("Hello {$problem['username']},\n\nWe are addressing the following issue:\n\n{$problem['message']}\n\nBest regards,\nSupport Team");
                                    ?>
                                    <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($problem['id']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($problem['username']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($problem['email']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($problem['message']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($problem['created_at']); ?></td>
                                        <td class="px-4 py-3">
                                            <span class="<?php echo $problem['isSolved'] ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300'; ?> px-2 py-1 rounded-full text-xs font-medium">
                                                <?php echo $problem['isSolved'] ? 'Solved' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="<?php echo $mailto_link; ?>" 
                                               class="inline-block bg-blue-800 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm">
                                                Send Mail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="text-center bg-gray-800 p-6 rounded-lg shadow-md">
                            <p class="text-gray-400">No problems to show!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </body>
            </html>
            <?php
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
        echo "ERROR!!!\n";
    }
}
else
{
    header("Location: login.php");
    exit();
}
?>