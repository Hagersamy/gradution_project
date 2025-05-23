<?php
session_start();
require_once 'conn.php'; 

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];
$role = $_SESSION['role'];

// Fetch the permissions for the logged-in role
$sql = "SELECT * FROM functionality_for_roles WHERE role = :role";
$stmt = $pdo->prepare($sql);
$stmt->execute(['role' => $role]);
$permissions = $stmt->fetch(PDO::FETCH_ASSOC);

// Check permissions for approving labs
if (!$permissions['approve_lab']) {
    die("You do not have permission to approve or disapprove labs.");
}

// Handle approve or disapprove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['lab_id'], $_POST['csrf_token'])) {
    // Validate CSRF token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }

    $lab_id = intval($_POST['lab_id']);
    $action = $_POST['action']; // 'approve' or 'disapprove'

    // Determine the status based on the action
    $status = ($action === 'approve') ? 1 : 0;

    $stmt = $conn->prepare("UPDATE labs SET approved = :status WHERE lab_id = :lab_id");
    if ($stmt->execute(['status' => $status, 'lab_id' => $lab_id])) {
        $message = $action === 'approve' ? "Lab approved successfully." : "Lab disapproved successfully.";
        echo "<script>alert('" . htmlspecialchars($message) . "');</script>";
    } else {
        echo "<script>alert('Failed to update lab status.');</script>";
    }
}

// Fetch labs that are not approved
$stmt = $pdo->prepare("SELECT lab_id, labname, laburl, severity FROM labs WHERE approved = 0");
$stmt->execute();
$labs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Labs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold">Labs Pending Approval</h1>
            </div>

            <?php if (empty($labs)): ?>
                <div class="p-6 text-center text-gray-500">
                    <p class="text-xl">No labs need approval.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lab URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($labs as $lab): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($lab['lab_id']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($lab['labname']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 hover:underline">
                                        <a href="<?= htmlspecialchars($lab['laburl']) ?>" target="_blank"><?= htmlspecialchars($lab['laburl']) ?></a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $severityClass = match($lab['severity']) {
                                            'Low' => 'bg-green-100 text-green-800',
                                            'Medium' => 'bg-yellow-100 text-yellow-800',
                                            'High' => 'bg-orange-100 text-orange-800',
                                            'Critical' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $severityClass ?>">
                                            <?= htmlspecialchars($lab['severity']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" class="inline-block mr-2">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="lab_id" value="<?= htmlspecialchars($lab['lab_id']) ?>">
                                            <button 
                                                type="submit" 
                                                name="action" 
                                                value="approve" 
                                                class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded-md transition duration-300"
                                            >
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="lab_id" value="<?= htmlspecialchars($lab['lab_id']) ?>">
                                            <button 
                                                type="submit" 
                                                name="action" 
                                                value="disapprove" 
                                                class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md transition duration-300"
                                            >
                                                Disapprove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>