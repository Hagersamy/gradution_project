<?php


// Database configuration
$dsn = 'mysql:host=localhost;dbname=android_pentest_academy';
$username = 'root';
$password = '';

try {
    // Create a PDO instance
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>