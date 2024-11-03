
<?php
require_once 'conn.php';
if(!isset($_SESSION['id']) || $_SESSION['role']!="support")
{
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = htmlspecialchars(trim($_POST["name"]));
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = htmlspecialchars(trim($_POST["message"]));
    }

    // Insert into database if no errors
    if (empty($name_err) && empty($email_err) && empty($message_err)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact (name, email, message) VALUES (:name, :email, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "thank you";
                header("Location: home.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Android Pentest Academy</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #000;
            color: #00ff00;
            padding: 2rem;
        }

        /* Form Styles */
        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            border: 1px solid #1d1d1a;
        }

        .contact-form h2 {
            color: #D3FF00;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #D3FF00;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #1d1d1a;
            border-radius: 5px;
            background-color: #222;
            color: #00ff00;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: yellow;
            outline: none;
        }

        .error {
            color: red;
            font-size: 0.9rem;
        }

        .submit-button {
            background-color: yellow;
            color: black;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #aeff00;
        }
    </style>
</head>
<body>

    <div class="contact-form">
        <h2>Contact Us</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="">
                <span class="error"><?php if(!empty($name_err)) echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="">
                <span class="error"><?php if(!empty($email_err)) echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5"></textarea>
                <span class="error"><?php if(!empty($message_err)) echo $message_err; ?></span>
            </div>
            <button type="submit" class="submit-button">Send Message</button>
        </form>
    </div>

</body>
</html>