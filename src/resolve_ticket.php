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
    <title>Support Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-900 text-white">
<nav class="bg-black text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-2xl font-bold">Android Pentest Academy</div>
        <ul class="flex space-x-4">
            <li><a href="home.php" class="hover:text-blue-400">Home</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="userdashboard.php" class="hover:text-blue-400">Dashboard</a></li>
                <li><a href="assistant.php" class="hover:text-secondary transition duration-300 ease-in-out">AI BOT</a></li>
                <li><a href="logout.php" class="hover:text-blue-400">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Support Tickets</h1>
    
    <?php if($problems): ?>
        <div class="overflow-x-auto">
            <table class="w-full bg-gray-800 shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-700 text-gray-300">
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Message</th>
                        <th class="px-4 py-3 text-left">Submitted</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($problems as $problem): ?>
                        <?php 
                        $mailto_link = "mailto:{$problem['email']}?subject=" . urlencode("Regarding your problem: {$problem['id']}") . "&body=" . urlencode("Hello {$problem['username']},\n\nWe are addressing the following issue:\n\n{$problem['message']}\n\nBest regards,\nSupport Team");
                        ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-700">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($problem['id']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($problem['username']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($problem['email']); ?></td>
                            <td class="px-4 py-3" title="<?php echo htmlspecialchars($problem['message']); ?>">
                                <?php 
                                    $message = htmlspecialchars($problem['message']);
                                    echo strlen($message) > 40 ? substr($message, 0, 30) . '...' : $message;
                                ?>
                            </td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($problem['created_at']); ?></td>
                            <td class="px-4 py-3">
                                <form action="toggle_status.php" method="POST">
                                    <input type="hidden" name="ticket_id" value="<?php echo $problem['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $problem['isSolved']; ?>">
                                    <button type="submit" class="px-2 py-1 rounded-full text-xs font-medium focus:outline-none <?php echo $problem['isSolved'] ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300'; ?>">
                                        <?php echo $problem['isSolved'] ? 'Solved' : 'Pending'; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?php echo $mailto_link; ?>" 
                                   class="bg-blue-800 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
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
            die("Access denied.");
        }
    }
    catch(PDOException $e)
    {
        echo "ERROR: " . $e->getMessage();
    }
}
else
{
    header("Location: login.php");
    exit();
}
?>
