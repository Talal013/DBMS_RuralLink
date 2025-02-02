<?php
include 'db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$user_id = $_GET['id'];
$stmt = $conn->prepare("UPDATE users SET is_blocked = 0 WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
header("Location: admin_dashboard.php");
?>
