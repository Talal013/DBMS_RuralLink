<?php
include 'db.php';
session_start();

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $amount = $_POST['amount'];

    $agent_commission = $amount * 0.05;
    $admin_commission = $amount * 0.03;

    $stmt = $conn->prepare("INSERT INTO payments (job_id, amount, agent_commission, admin_commission) VALUES (?, ?, ?, ?)");
    $stmt->execute([$job_id, $amount, $agent_commission, $admin_commission]);

    echo "Payment processed successfully!";
}
?>
<form method="POST">
    <label for="job_id">Job ID:</label>
    <input type="number" name="job_id" required>
    <label for="amount">Amount:</label>
    <input type="number" name="amount" step="0.01" required>
    <button type="submit">Pay</button>
</form>
