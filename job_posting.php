<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect job post data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $area = $_POST['location'];
    $job_type = $_POST['job_type'];
    $amount = $_POST['amount'];
    $client_id = $_SESSION['user_id'];

    // Insert into the database
    $sql = "INSERT INTO job_posting (title, description, location, job_type, amount, client_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdi", $title, $description, $area, $job_type, $amount, $client_id);

    if ($stmt->execute()) {
        // Job post successful
        $_SESSION['success_message'] = "Job posted successfully!";
        header("Location: client_dashboard.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - RuralLink</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .job-post-container {
            width: 400px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .job-post-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .job-post-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .job-post-container input,
        .job-post-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .job-post-container textarea {
            resize: vertical;
        }

        .job-post-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .job-post-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="job-post-container">
        <h2>Post a Job</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="title">Job Title:<span class="required">*</span></label>
            <input type="text" id="title" name="title" required>

            <label for="description">Job Description:<span class="required">*</span></label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="location">Area:<span class="required">*</span></label>
            <input type="text" id="location" name="location" required>

            <label for="job_type">Job Type:<span class="required">*</span></label>
            <input type="text" id="job_type" name="job_type" required>

            <label for="amount">Amount (in Taka):<span class="required">*</span></label>
            <input type="number" id="amount" name="amount" step="1" required>

            <button type="submit">Post Job</button>
        </form>
    </div>
</body>

</html>