<?php
session_start();
require 'conn.php';
require 'utils.php';

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$role = $_SESSION['role'];
$permissions = getPermissions($pdo, $role);

if (!$permissions || !$permissions['edit_users']) {
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
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
                    <h1 class="text-3xl font-bold text-red-500 mb-4">Invalid UserID</h1>
                    <p class="text-gray-300 mb-6">ID Must Be Numeric.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="/project/user_managment.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            Go to Users
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

    $userId = (int)htmlspecialchars($_GET['id']);
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $sql = "SELECT role FROM functionality_for_roles";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        die("CSRF validation failed.");
    }

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
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
                    <h1 class="text-3xl font-bold text-red-500 mb-4">Invalid UserID</h1>
                    <p class="text-gray-300 mb-6">ID Must Be Numeric.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="/project/user_managment.php" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">
                            Go to Users
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

    $userId = (int)htmlspecialchars($_POST['id']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    $sql = "SELECT role FROM functionality_for_roles";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $validRoles = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'role');

    if (!in_array($role, $validRoles)) {
        die("Invalid role selected.");
    }

    $sql = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $userid,PARAM_INT);
    $stmt->execute();

    header('Location: listusers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-900 min-h-screen flex items-center justify-center p-4 text-gray-200">
    <div class="bg-zinc-800 shadow-2xl rounded-lg w-full max-w-md p-8 border border-zinc-700">
        <h1 class="text-3xl font-bold text-emerald-400 mb-6 text-center">Edit User</h1>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="<?php echo htmlspecialchars($user['username']); ?>" 
                    required 
                    class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-100"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="<?php echo htmlspecialchars($user['email']); ?>" 
                    required 
                    class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-100"
                >
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                <select 
                    name="role" 
                    id="role" 
                    required 
                    class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-100"
                >
                    <?php foreach ($roles as $roleOption): ?>
                        <option 
                            value="<?php echo htmlspecialchars($roleOption['role']); ?>"
                            <?php echo $roleOption['role'] == $user['role'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($roleOption['role']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end mt-6">
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50 font-semibold"
                >
                    Update User
                </button>
            </div>
        </form>
    </div>

    <script>
        // Client-side validation with dark mode styling
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const role = document.getElementById('role');

            // Reset previous error states
            [username, email, role].forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-zinc-600');
            });

            let hasError = false;

            if (!username.value.trim()) {
                hasError = true;
                username.classList.remove('border-zinc-600');
                username.classList.add('border-red-500');
            }

            if (!email.value.trim() || !email.value.includes('@')) {
                hasError = true;
                email.classList.remove('border-zinc-600');
                email.classList.add('border-red-500');
            }

            if (!role.value) {
                hasError = true;
                role.classList.remove('border-zinc-600');
                role.classList.add('border-red-500');
            }

            if (hasError) {
                e.preventDefault();
                // Use a dark mode friendly alert or custom modal
                alert('Please correct the errors in the form');
            }
        });
    </script>
</body>
</html>