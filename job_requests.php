<?php
// Backend Logic
include 'db.php';
session_start();

// Restrict access to agents only
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agent') {
    header("Location: login.php");
    exit;
}

// Fetch Open Jobs
$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM job_requests WHERE status = 'open' AND (title LIKE ? OR description LIKE ?)");
$search_term = "%$search%";
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle Job Acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_job'])) {
    $job_id = $_POST['job_id'];
    $stmt = $conn->prepare("UPDATE job_requests SET status = 'accepted', accepted_by = ? WHERE id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $job_id);
    $stmt->execute();
    $stmt->close();
    echo "<p class='message'>Job accepted successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #45a049;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        /* Job Search */
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-bar button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }

        /* Job Listings */
        .job-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .job-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .job-item:hover {
            background-color: #f1f1f1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .job-item strong {
            font-size: 16px;
            color: #333;
        }

        .job-item .actions button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .job-item .actions button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Job Listings</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="log_out.php">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search jobs" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <ul class="job-list">
            <?php foreach ($jobs as $job): ?>
                <li class="job-item">
                    <div>
                        <strong><?= htmlspecialchars($job['title']) ?></strong>
                        <p><?= htmlspecialchars($job['location']) ?></p>
                    </div>
                    <div class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                            <button type="submit" name="accept_job">Accept</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>