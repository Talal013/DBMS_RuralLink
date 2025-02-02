<?php
include 'db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$job_id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM job_posting WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
header("Location: admin_dashboard.php");
?>
