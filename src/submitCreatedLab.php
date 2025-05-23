<?php
session_start();
include 'conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must log in to submit a lab.";
    exit;
}

// Fetch the user's role from the database
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$role = $stmt->fetchColumn();
$stmt->fetch();
//$stmt->close();

// Restrict access for unauthorized roles
if ($role !== 'Admin' && $role !== 'Creator') {
    echo "You do not have permission to create labs.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $labname = $_POST['labname'];
    $severity = $_POST['severity'];
    $laburl = $_POST['laburl'];
    $description = $_POST['description'];

    // Validate inputs
    if (empty($labname) || empty($severity) || empty($laburl) || empty($description)) {
        echo "All fields are required.";
        exit;
    }

    if (!filter_var($laburl, FILTER_VALIDATE_URL)) {
        echo "Invalid lab URL.";
        exit;
    }

    // Insert lab into the database
    $stmt = $pdo->prepare("INSERT INTO labs (labname, severity, laburl, description, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $labname, $severity, $laburl, $description, $userId);

    if ($stmt->execute()) {
        echo "Lab created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
