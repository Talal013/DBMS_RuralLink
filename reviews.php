<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $reviewee_id = $_POST['reviewee_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO reviews (job_id, reviewer_id, reviewee_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$job_id, $_SESSION['user_id'], $reviewee_id, $rating, $comment]);

    echo "Review submitted successfully!";
}
?>
<form method="POST">
    <label for="job_id">Job ID:</label>
    <input type="number" name="job_id" required>
    <label for="reviewee_id">Reviewee ID:</label>
    <input type="number" name="reviewee_id" required>
    <label for="rating">Rating:</label>
    <input type="number" name="rating" min="1" max="5" required>
    <label for="comment">Comment:</label>
    <textarea name="comment"></textarea>
    <button type="submit">Submit</button>
</form>
