<?php
session_start();
require 'conn.php';
require 'utils.php';

if (!isset($_SESSION['role']) || !isset($_POST['id']) || !isset($_POST['csrf_token'])) {
    header('Location: login.php');
    exit;
}

$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
htmlspecialchars(($_POST['id']));
$csrfToken = htmlspecialchars($_POST['csrf_token']);

if ($csrfToken !== $_SESSION['csrf_token']) {
    die("CSRF validation failed.");
}

$role = filter_var($_SESSION['role'], FILTER_SANITIZE_STRING);
$permissions = getPermissions($pdo, $role);

if (!$permissions || empty($permissions['delete_users'])) {
    die("You do not have permission to delete users.");
}

$sql = "DELETE FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$flag=$stmt->fetch(PDO::FETCH_ASSOC);

header('Location: user_managment.php');
exit;