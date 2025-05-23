<?php
session_start();
if(isset($_SESSION["user_id"])) {
    require_once "conn.php";
    try {
        // Check if the ticket ID and current status are set in the form
        if (isset($_POST['ticket_id']) && isset($_POST['current_status'])) {
            $ticket_id = $_POST['ticket_id'];
            $current_status = $_POST['current_status'];

            // Toggle the status: if it's 1 (Solved), set it to 0 (Pending), and vice versa
            $new_status = ($current_status == 1) ? 0 : 1;

            // Update the ticket's status in the database
            $sql = "UPDATE contact SET isSolved = :new_status WHERE id = :ticket_id";
            $stat = $pdo->prepare($sql);
            $stat->bindParam(':new_status', $new_status);
            $stat->bindParam(':ticket_id', $ticket_id);
            $stat->execute();

            // Redirect back to the support tickets page
            header("Location: resolve_ticket.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
