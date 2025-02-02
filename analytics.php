<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $conn->query("SELECT COUNT(*) as total_jobs, SUM(amount) as total_earnings FROM payments");
$data = $stmt->fetch();

echo "Total Jobs: " . $data['total_jobs'];
echo "Total Earnings: $" . $data['total_earnings'];
?>
