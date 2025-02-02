<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$referral_code = $_SESSION['referral_code'];

// Fetch Jobs Posted by the Client
$stmt = $conn->prepare("SELECT * FROM job_posting WHERE client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Reviews
$stmt = $conn->prepare("
    SELECT r.rating, r.comment, u.first_name, u.last_name, r.created_at 
    FROM reviews r
    JOIN users u ON r.reviewer_id = u.id
    WHERE r.reviewee_id = ?
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Referral Rewards
$stmt = $conn->prepare("
    SELECT SUM(reward_amount) AS total_rewards 
    FROM referrals 
    WHERE referred_by = ?
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$referral_rewards = $stmt->get_result()->fetch_assoc()['total_rewards'] ?? 0;
$stmt->close();

// Handle Job Posting Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_job'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("
        INSERT INTO job_posting (client_id, title, description, location, amount, status) 
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("isssi", $client_id, $title, $description, $location, $amount);

    if ($stmt->execute()) {
        $success_message = "Job posted successfully!";
    } else {
        $error_message = "Failed to post the job. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Client Dashboard</title>
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

        .payment-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 30px;
        }

        .jobs-table th {
            background-color: #FF6F00;
            color: white;
        }

        .tab-content {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="client_dashboard.php">Client Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($referral_code): ?>
                        <li class="nav-item">
                            <span class="nav-link text-warning">Referral Code:
                                <?= htmlspecialchars($referral_code, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#jobs">JOBS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log_out.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Tabs for Sections -->
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button"
                    role="tab" aria-controls="jobs" aria-selected="true">Jobs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button"
                    role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rewards-tab" data-bs-toggle="tab" data-bs-target="#rewards" type="button"
                    role="tab" aria-controls="rewards" aria-selected="false">Referral Rewards</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="post-job-tab" data-bs-toggle="tab" data-bs-target="#post-job" type="button"
                    role="tab" aria-controls="post-job" aria-selected="false">Post Job</button>
            </li>
        </ul>

        <div class="tab-content" id="dashboardTabsContent">
            <!-- Jobs Section -->
            <div class="tab-pane fade show active" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
                <h2 class="section-title">Your Job Posts</h2>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>
                <?php if (count($jobs) > 0): ?>
                    <table class="table table-striped jobs-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?= htmlspecialchars($job['title']) ?></td>
                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                    <td><?= htmlspecialchars($job['status']) ?></td>
                                    <td>
                                        <?php if ($job['status'] === 'accepted'): ?>
                                            <form method="POST" action="chat.php" class="d-inline">
                                                <input type="hidden" name="chat_with" value="<?= $job['accepted_by'] ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">Chat</button>
                                            </form>
                                            <a href="submit_review.php?job_id=<?= $job['id'] ?>&reviewee_id=<?= $job['accepted_by'] ?>"
                                                class="btn btn-success btn-sm">Review</a>
                                        <?php endif; ?>
                                        <a href="payment.php?job_id=<?= $job['id'] ?>" class="btn btn-warning btn-sm">Pay Now</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">You haven't posted any jobs yet.</p>
                <?php endif; ?>
            </div>

            <!-- Reviews Section -->
            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <h2 class="section-title">Reviews</h2>
                <?php if (count($reviews) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($reviews as $review): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></strong>
                                rated you <?= htmlspecialchars($review['rating']) ?>/5
                                <p class="mb-0"><?= htmlspecialchars($review['comment']) ?></p>
                                <small><?= htmlspecialchars($review['created_at']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No reviews yet.</p>
                <?php endif; ?>
            </div>

            <!-- Referral Rewards Section -->
            <div class="tab-pane fade" id="rewards" role="tabpanel" aria-labelledby="rewards-tab">
                <h2 class="section-title">Referral Rewards</h2>
                <div class="alert alert-success">
                    <strong>Total Rewards:</strong> You've earned ৳<?= number_format($referral_rewards, 2) ?> in discounts for your next hiring!
                </div>
            </div>

            <!-- Post Job Section -->
            <div class="tab-pane fade" id="post-job" role="tabpanel" aria-labelledby="post-job-tab">
                <h2 class="section-title">Post a Job</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="title" class="form-label">Job Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Job Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (৳)</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <button type="submit" name="post_job" class="btn btn-primary">Post Job</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
