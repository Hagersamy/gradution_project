<?php
require 'conn.php';
function getPermissions($pdo, $role) {
    $sql = "SELECT * FROM functionality_for_roles WHERE role = :role";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['role' => $role]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
