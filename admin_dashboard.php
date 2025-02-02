<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$referral_code = $_SESSION['referral_code'];

// Fetch Users
$stmt = $conn->prepare("SELECT id, first_name, email, role, is_blocked FROM users ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Jobs
$stmt = $conn->prepare("SELECT id, title, status FROM job_posting ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Reviews
$stmt = $conn->prepare("
    SELECT r.id, r.comment, r.rating, u.first_name AS reviewer_name 
    FROM reviews r
    JOIN users u ON r.reviewer_id = u.id
    ORDER BY r.created_at DESC LIMIT 5
");
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #FF6F00;
            color: white;
            font-weight: bold;
        }

        .btn-danger,
        .btn-warning,
        .btn-primary {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-warning">Referral Code: <?= htmlspecialchars($referral_code) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log_out.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- User Management Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">User Management</div>
                    <div class="card-body">
                        <p><strong>Recent Users:</strong></p>
                        <ul class="list-group">
                            <?php foreach ($users as $user): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?= htmlspecialchars($user['first_name']) ?> - 
                                        <small><?= htmlspecialchars($user['email']) ?></small>
                                        <span class="badge bg-info"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
                                    </div>
                                    <div>
                                        <?php if ($user['is_blocked']): ?>
                                            <a href="unblock_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Unblock</a>
                                        <?php else: ?>
                                            <a href="block_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Block</a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="manage_users.php" class="btn btn-primary btn-sm mt-3">Manage All Users</a>
                    </div>
                </div>
            </div>

            <!-- Job Management Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Job Management</div>
                    <div class="card-body">
                        <p><strong>Recent Jobs:</strong></p>
                        <ul class="list-group">
                            <?php foreach ($jobs as $job): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?= htmlspecialchars($job['title']) ?> - 
                                        <small><?= htmlspecialchars(ucfirst($job['status'])) ?></small>
                                    </div>
                                    <a href="delete_job.php?id=<?= $job['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="manage_jobs.php" class="btn btn-primary btn-sm mt-3">Manage All Jobs</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Management Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Review Management</div>
                    <div class="card-body">
                        <p><strong>Recent Reviews:</strong></p>
                        <ul class="list-group">
                            <?php foreach ($reviews as $review): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($review['reviewer_name']) ?>:</strong> 
                                        <?= htmlspecialchars($review['comment']) ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($review['rating']) ?> Stars</span>
                                    </div>
                                    <a href="delete_review.php?id=<?= $review['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="manage_reviews.php" class="btn btn-primary btn-sm mt-3">Manage All Reviews</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
