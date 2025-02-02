<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection
$user_id = $_SESSION['user_id'];
$referral_code = $_SESSION['referral_code'];


// Fetch Referral Rewards
$stmt = $conn->prepare("
    SELECT SUM(reward_amount) AS total_rewards 
    FROM referrals 
    WHERE referred_by = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$referral_rewards = $stmt->get_result()->fetch_assoc()['total_rewards'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard - RuralLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }

        header {
            background-color: #FF6F00;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 28px;
            font-weight: bold;
        }

        nav {
            background-color: #333;
            padding: 15px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #FF6F00;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #FF6F00;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background-color: #FF6F00;
            color: white;
        }

        td {
            background-color: #fafafa;
        }

        tr:hover td {
            background-color: #f0f0f0;
        }

        .action-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #FF6F00;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #e65c00;
        }

        .completed {
            color: green;
            font-weight: bold;
        }

        .section {
            margin-top: 40px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .section p {
            font-size: 18px;
            color: #555;
        }


        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;

        }

        footer p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <header>
        Worker Dashboard - RuralLink
    </header>

    <nav>
        <a href="assigned_jobs.php">Assigned Jobs</a>
        <a href="log_out.php">Logout</a>
    </nav>
    <div class="alert alert-success mb-4">
        <strong>Referral Rewards:</strong> You've earned ৳<?= number_format($referral_rewards, 2) ?> in discounts
        for your
        next hiring!
    </div>
    <main>


        <section>

            <h2>Assigned Jobs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch assigned jobs
                    $query = "SELECT * FROM job_posting WHERE worker_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['area']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <?php if ($row['status'] === 'ongoing'): ?>
                                    <a href="mark_complete.php?job_id=<?php echo $row['id']; ?>" class="action-btn">Mark as
                                        Complete</a>
                                <?php endif; ?>
                                <?php if ($row['status'] === 'completed'): ?>
                                    <span class="completed">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- View Reviews Section -->
        <section>
            <h2>Your Reviews</h2>
            <table>
                <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Job ID</th>
                        <th>Rating</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch reviews for the worker
                    $query = "SELECT * FROM reviews WHERE reviewer_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['job_id']; ?></td>
                            <td><?php echo $row['rating']; ?></td>
                            <td><?php echo $row['comment']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Payment Section -->
        <section>
            <h2>Your Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Job ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch payments for the worker
                    $query = "SELECT * FROM payment WHERE worker_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['job_id']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['payment_date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 RuralLink. All Rights Reserved.</p>
    </footer>
</body>

</html>