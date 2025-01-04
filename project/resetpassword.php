<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if the token is valid and not expired
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expiry > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resetRequest) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update the user's password
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $resetRequest['email']);
            $stmt->execute();

            // Delete the token from the database
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $success = "Password successfully reset. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Invalid or expired token.";
        }
    }
} else {
    $token = $_GET['token'] ?? null;
    if (!$token) {
        die("Invalid request.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #00ff00;
            text-align: center;
            padding: 50px;
        }
        .form-container {
            background: #1a1a1a;
            padding: 20px;
            margin: auto;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        input, button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        input {
            background: #111;
            color: #fff;
        }
        button {
            background: yellow;
            color: black;
            cursor: pointer;
        }
        button:hover {
            background: #aeff00;
        }
        .error, .success {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Reset Your Password</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php elseif (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
            <input type="password" name="password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
