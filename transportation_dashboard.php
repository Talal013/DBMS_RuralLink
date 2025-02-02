<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'transportation') {
  header("Location: login.php");
  exit;
}

$transportation_id = $_SESSION['user_id'];
$accepted_by = $_SESSION['accepted_by'];
$referral_code = $_SESSION['referral_code'];


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $accepted_by);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();
$stmt->close();


// Fetch Referral Rewards
$stmt = $conn->prepare("
SELECT SUM(reward_amount) AS total_rewards
FROM referrals
WHERE referred_by = ?
");
$stmt->bind_param("i", $transportation_id);
$stmt->execute();
$referral_rewards = $stmt->get_result()->fetch_assoc()['total_rewards'] ?? 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Transportation Dashboard</title>
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
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="client_dashboard.php">Transportation Dashboard</a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link text-warning">Referral Code:
              <?php echo $referral_code ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="log_out.php">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <!-- Referral Rewards -->
    <div class="alert alert-success mb-4">
      <strong>Referral Rewards:</strong> You've earned ৳<?= number_format($referral_rewards, 2) ?> in discounts
      for your
      next hiring!
    </div>

    <!-- Job Management Section -->
    <div class="card mb-4">
      <div class="card-header">Your Agent</div>
      <div class="card-body">
        <p><strong>Name:</strong> <?= htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']) ?>
        </p>
        <p><strong>Email:</strong> <?= htmlspecialchars($agent['email']) ?></p>

        <form method="POST" action="chat.php">
          <input type="hidden" name="chat_with" value="<?= $agent['id'] ?>">
          <button type="submit" class="btn btn-primary btn-sm">Chat with Agent</button>
        </form>


      </div>
    </div>
  </div>
</body>

</html>