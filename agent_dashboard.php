<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agent') {
    header("Location: login.php");
    exit;
}

$agent_id = $_SESSION['user_id'];
$referral_code = $_SESSION['referral_code'];

// Fetch Accepted Jobs
$stmt = $conn->prepare("SELECT * FROM job_posting WHERE accepted_by = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$accepted_jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Accepted Transportations
$stmt = $conn->prepare("SELECT * FROM users WHERE accepted_by = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$accepted_trans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Workers Managed by the Agent
$stmt = $conn->prepare("SELECT * FROM worker WHERE agent_id = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$workers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch Notifications
$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_assoc();
$unread_count = $notifications['unread_count'];

// Fetch Referral Rewards
$stmt = $conn->prepare("SELECT SUM(reward_amount) AS total_rewards FROM referrals WHERE referred_by = ?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$referral_rewards = $stmt->get_result()->fetch_assoc()['total_rewards'] ?? 0;

// Fetch Reviews
$stmt = $conn->prepare("
    SELECT r.rating, r.comment, u.first_name, u.last_name, r.created_at 
    FROM reviews r
    JOIN users u ON r.reviewer_id = u.id
    WHERE r.reviewee_id = ?
");
$stmt->bind_param("i", $agent_id);
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
    <title>Agent Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .notification-badge {
            background-color: red;
            color: white;
            font-size: 12px;
            border-radius: 50%;
            padding: 5px 10px;
            position: relative;
            top: -5px;
            right: 5px;
            display: inline-block;
        }

        .card-header {
            background-color: #FF6F00;
            color: white;
            font-weight: bold;
        }

        .tab-content {
            margin-top: 20px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="agent_dashboard.php">Agent Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-warning">Referral Code: <?= htmlspecialchars($referral_code) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="job_list.php">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pending_transportations.php">Transportation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat_list.php">Messages 
                            <span id="notification-badge" class="notification-badge"><?= $unread_count ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log_out.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Tabs for Dashboard Sections -->
        <ul class="nav nav-tabs" id="agentDashboardTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="accepted-jobs-tab" data-bs-toggle="tab" data-bs-target="#accepted-jobs" type="button" role="tab">Accepted Jobs</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="accepted-transportations-tab" data-bs-toggle="tab" data-bs-target="#accepted-transportations" type="button" role="tab">Transportations</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="workers-tab" data-bs-toggle="tab" data-bs-target="#workers" type="button" role="tab">Workers</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="referrals-tab" data-bs-toggle="tab" data-bs-target="#referrals" type="button" role="tab">Referral Rewards</button>
            </li>
        </ul>

        <div class="tab-content" id="agentDashboardTabsContent">
            <!-- Accepted Jobs -->
            <div class="tab-pane fade show active" id="accepted-jobs" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header">Accepted Jobs</div>
                    <div class="card-body">
                        <?php if (count($accepted_jobs) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($accepted_jobs as $job): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($job['title']) ?></strong>
                                            <p><?= htmlspecialchars($job['location']) ?></p>
                                            <small>Status: <?= htmlspecialchars($job['status']) ?></small>
                                        </div>
                                        <form method="POST" action="chat.php">
                                            <input type="hidden" name="chat_with" value="<?= $job['client_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Chat with Client</button>
                                        </form>
                                        <?php if ($job['status'] === 'accepted'): ?>
                                            <a href="submit_review.php?job_id=<?= $job['id'] ?>&reviewee_id=<?= $job['client_id'] ?>" class="btn btn-success btn-sm">Submit Review</a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No accepted jobs yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Accepted Transportations -->
            <div class="tab-pane fade" id="accepted-transportations" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header">Accepted Transportations</div>
                    <div class="card-body">
                        <?php if (count($accepted_trans) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($accepted_trans as $item): ?>
                                    <li class="list-group-item">
                                        <strong>Name:</strong> <?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?><br>
                                        <strong>Email:</strong> <?= htmlspecialchars($item['email']) ?><br>
                                        <small>Status: <?= htmlspecialchars($item['status']) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No transportations found yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workers -->
            <div class="tab-pane fade" id="workers" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header">Registered Workers</div>
                    <div class="card-body">
                        <?php if (count($workers) > 0): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Skill</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($workers as $worker): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($worker['name']) ?></td>
                                            <td><?= htmlspecialchars($worker['email']) ?></td>
                                            <td><?= htmlspecialchars($worker['skill']) ?></td>
                                            <td><?= htmlspecialchars($worker['rating']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No registered workers found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header">Reviews</div>
                    <div class="card-body">
                        <?php if (count($reviews) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($reviews as $review): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></strong> rated you <?= htmlspecialchars($review['rating']) ?>/5
                                        <p><?= htmlspecialchars($review['comment']) ?></p>
                                        <small><?= htmlspecialchars($review['created_at']) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No reviews yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Referral Rewards -->
            <div class="tab-pane fade" id="referrals" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header">Referral Rewards</div>
                    <div class="card-body">
                        <p><strong>Total Rewards Earned:</strong> ৳<?= number_format($referral_rewards, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
