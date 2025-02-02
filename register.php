<?php
session_start();
include 'db.php'; // Include database connection

include 'middleware/is_logged_in.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $nid = $_POST['nid'];
    $referredBy = isset($_POST['referral_code']) ? $_POST['referral_code'] : null;
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $referral_code = rand(10000, 99999);

    // Insert into the database
    $sql = "INSERT INTO users (first_name, last_name, email, phone, nid, password, role, address, referral_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $first_name, $last_name, $email, $phone, $nid, $password, $role, $address, $referral_code);


    if ($stmt->execute()) {
        $referred_user_id = $stmt->insert_id;

        if ($referredBy) {
            $sql = "SELECT id FROM users WHERE referral_code = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $referredBy);
            $stmt->execute();
            $result = $stmt->get_result();


            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $referred_by_user_id = $row['id'];

                $reward_points = 10;
                $reward_amount = 5.00;

                $insert_sql = "INSERT INTO referrals (referred_by, referred_user, reward_points, reward_amount, created_at) 
                               VALUES (?, ?, ?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iiid", $referred_by_user_id, $referred_user_id, $reward_points, $reward_amount);
                $insert_stmt->execute();

            } else {
                echo "Invalid referral code!";
                exit();
            }
        }

        // Registration successful
        $_SESSION['success_message'] = "Registration successful! Please log in.";
        header("Location: login.php");
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
    <title>Sign Up - RuralLink</title>
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

        .signup-container {
            height: 90vh;

            width: 400px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .signup-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .signup-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .signup-container input,
        .signup-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .signup-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .signup-container button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        .signup-container p {
            text-align: center;
        }

        .required {
            color: red;
        }

        #worker-fields {
            display: none;
        }
    </style>
</head>

<body>
    <div class="signup-container">
        <h2>Registration Info</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="first_name">First Name:<span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:<span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="email">Email:<span class="required">*</span></label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone:<span class="required">*</span></label>
            <input type="text" id="phone" name="phone" required>

            <label for="nid">NID:<span class="required">*</span></label>
            <input type="number" id="nid" name="nid" required>

            <label for="referral_code">Referral Code:</label>
            <input type="number" id="referral_code" name="referral_code">

            <label for="password">Password:<span class="required">*</span></label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:<span class="required">*</span></label>
            <input type="password" id="confirm_password" required>

            <label for="address">Address:<span class="required">*</span></label>
            <input type="text" id="address" name="address" required>






            <label for="role">Role:<span class="required">*</span></label>
            <select id="role" name="role" required onchange="toggleWorkerFields()">
                <option value="" disabled selected>Select Role</option>
                <option value="client">Client</option>
                <option value="agent">Agent</option>
                <option value="worker">Worker</option>
                <option value="admin">Admin</option>
                <option value="transportation">Transportation</option>
            </select>

            <div id="worker-fields">
                <label for="address">Address:<span class="required">*</span></label>
                <input type="text" id="address" name="address">

                <label for="type_of_work">Type of Skill:<span class="required">*</span></label>
                <input type="text" id="type_of_work" name="type_of_work">
            </div>

            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>

    <script>
        function toggleWorkerFields() {
            const role = document.getElementById('role').value;
            const workerFields = document.getElementById('worker-fields');
            if (role === 'worker') {
                workerFields.style.display = 'block';
                document.getElementById('address').setAttribute('required', 'required');
                document.getElementById('type_of_work').setAttribute('required', 'required');
            } else {
                workerFields.style.display = 'none';
                document.getElementById('address').removeAttribute('required');
                document.getElementById('type_of_work').removeAttribute('required');
            }
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>

</html>