<?php
session_start();
include 'db.php';

if (!isset($_GET['job_id'], $_GET['reviewee_id'])) {
  echo "Invalid request.";
  exit;
}

$job_id = $_GET['job_id'];
$reviewee_id = $_GET['reviewee_id'];
$reviewer_id = $_SESSION['user_id'] ?? null;

if (!$reviewer_id) {
  echo "Please log in to submit a review.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rating = $_POST['rating'];
  $comment = $_POST['comment'];

  if ($rating < 1 || $rating > 5) {
    echo "Invalid rating. Please select a rating between 1 and 5.";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO reviews (job_id, reviewer_id, reviewee_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param("iiiis", $job_id, $reviewer_id, $reviewee_id, $rating, $comment);

  if ($stmt->execute()) {
    echo "Review submitted successfully.";
    exit;
  } else {
    echo "Error submitting review.";
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Review</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: linear-gradient(135deg, #007bff, #0056b3);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      font-size: 1.8rem;
      text-align: center;
      margin-bottom: 20px;
      color: #ffffff;
    }

    label {
      font-weight: bold;
      color: #ffffff;
    }

    .form-control {
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      background: #ff6f00;
      color: white;
      border: none;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn:hover {
      background-color: #e65c00;
      transform: scale(1.03);
    }

    .btn:focus {
      outline: none;
      box-shadow: 0px 0px 5px rgba(255, 111, 0, 0.8);
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Submit Your Review</h1>
    <form method="POST">
      <div class="form-group">
        <label for="rating">Rating (1-5):</label>
        <select name="rating" id="rating" class="form-control" required>
          <option value="" disabled selected>Select a rating</option>
          <option value="1">1 Star</option>
          <option value="2">2 Stars</option>
          <option value="3">3 Stars</option>
          <option value="4">4 Stars</option>
          <option value="5">5 Stars</option>
        </select>
      </div>
      <div class="form-group">
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Write your feedback here..." required></textarea>
      </div>
      <button type="submit" class="btn">Submit Review</button>
    </form>
  </div>
</body>

</html>
