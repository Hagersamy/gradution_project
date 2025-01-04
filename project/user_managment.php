<?php
session_start();
require 'conn.php';
require 'utils.php';

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$role = filter_var($_SESSION['role'], FILTER_SANITIZE_STRING);
$permissions = getPermissions($pdo, $role);

if (!$permissions || !$permissions['see_all_users']) {
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
    exit;
}

$canEditUsers = !empty($permissions['edit_users']);
$canDeleteUsers = !empty($permissions['delete_users']);

$sql = "SELECT * FROM users ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 text-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-400 flex items-center">
                <i class="fas fa-users mr-3"></i>
                User Management
            </h1>
            <?php if ($canEditUsers): ?>
                <a href="createuser.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create New User
                </a>
            <?php endif; ?>
        </div>

        <?php if ($users): ?>
            <div class="overflow-x-auto shadow-2xl rounded-lg">
                <table class="w-full bg-gray-800 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-700 text-gray-300 uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 text-left">
                                <i class="fas fa-hashtag mr-2"></i>ID
                            </th>
                            <th class="px-4 py-3 text-left">
                                <i class="fas fa-user mr-2"></i>Username
                            </th>
                            <th class="px-4 py-3 text-left">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </th>
                            <th class="px-4 py-3 text-left">
                                <i class="fas fa-tag mr-2"></i>Role
                            </th>
                            <?php if ($canEditUsers || $canDeleteUsers): ?>
                                <th class="px-4 py-3 text-center">
                                    <i class="fas fa-cogs mr-2"></i>Actions
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['id']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <?php if ($canEditUsers || $canDeleteUsers): ?>
                                    <td class="px-4 py-3 flex-col gap-4 text-center space-x-2">
                                        <?php if ($canEditUsers): ?>
                                            <a href="editusers.php?id=<?php echo $user['id']; ?>" 
                                               class=" bg-blue-600 text-white  my-3 py-1 rounded-md hover:bg-blue-500 transition-colors duration-200 text-sm px-3">
                                                <i class="fas fa-edit mr-1"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($canDeleteUsers): ?>
                                            <form method="POST" action="deleteusers.php" class="inline-block">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                <button type="submit" 
                                                        class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-500 transition-colors duration-200 text-sm flex items-center justify-center">
                                                    <i class="fas fa-trash-alt mr-1"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center bg-gray-800 p-8 rounded-lg shadow-2xl">
                <i class="fas fa-user-slash text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-xl">No users found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>