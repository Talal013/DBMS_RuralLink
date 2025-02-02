<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'agent') {
    header("Location: login.php");
    exit;
}

$agent_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $pickup_time = $_POST['pickup_time'];

    $stmt = $conn->prepare("INSERT INTO transportation (agent_id, job_id, pickup_time) VALUES (?, ?, ?)");
    $stmt->execute([$agent_id, $job_id, $pickup_time]);

    echo "Transportation scheduled successfully!";
}

$stmt = $conn->prepare("SELECT * FROM transportation WHERE agent_id = ?");
$stmt->execute([$agent_id]);
$transportation = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Transportation Management</title>
</head>

<body>
    <h1>Transportation Management</h1>
    <h2>Schedule Transportation</h2>
    <form method="POST">
        <label for="job_id">Job ID:</label>
        <input type="number" name="job_id" required>
        <label for="pickup_time">Pickup Time:</label>
        <input type="datetime-local" name="pickup_time" required>
        <button type="submit">Schedule</button>
    </form>
    <h2>Scheduled Transportation</h2>
    <ul>
        <?php foreach ($transportation as $item): ?>
            <li>
                Job ID: <?= htmlspecialchars($item['job_id']) ?> - Pickup: <?= htmlspecialchars($item['pickup_time']) ?>
                <?= $item['dropoff_time'] ? '- Dropoff: ' . htmlspecialchars($item['dropoff_time']) : '' ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        Client Dashboard
    </header>
    <nav>
        <a href="post_job.php">Post a Job</a>
        <a href="view_jobs.php">View Jobs</a>
        <a href="log_out.php">Logout</a>
    </nav>
    <main>
        <h2>Welcome, Client!</h2>
        <div class="section">
            <p>Post jobs and manage your postings with ease.</p>
        </div>
    </main>
</body>

</html>