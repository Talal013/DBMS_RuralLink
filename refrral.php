<?php
include 'dbs.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referrer_id = $_SESSION['user_id'];
    $referee_email = $_POST['referee_email'];

    // Check if referee exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$referee_email]);
    $referee = $stmt->fetch();

    if ($referee) {
        // Insert referral
        $stmt = $conn->prepare("INSERT INTO referrals (referrer_id, referee_id, reward_amount) VALUES (?, ?, ?)");
        $stmt->execute([$referrer_id, $referee['id'], 10.00]); // Assume $10 reward for simplicity

        echo "Referral successfully added!";
    } else {
        echo "The referred user does not exist.";
    }
}
?>
<form method="POST" action="referrals.php">
    <label for="referee_email">Referee Email:</label>
    <input type="email" name="referee_email" required>
    <button type="submit">Refer</button>
</form>
<?php
$user_id = $_GET['user_id'];

$stmt = $conn->prepare("SELECT * FROM reviews WHERE reviewee_id = ?");
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();

foreach ($reviews as $review) {
    echo "<p>Rating: {$review['rating']}</p>";
    echo "<p>Comment: {$review['comment']}</p>";
}
?>
