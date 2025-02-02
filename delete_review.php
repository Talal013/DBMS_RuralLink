<?php
include 'db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$review_id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
header("Location: admin_dashboard.php");
?>
