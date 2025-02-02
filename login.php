<?php
include 'db.php'; // Database connection
session_start();

include 'middleware/is_logged_in.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $query = "SELECT * FROM users WHERE email = ? AND role = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['referral_code'] = $user['referral_code'];

        // Redirect to respective dashboard
        switch ($role) {
            case 'client':
                header('Location: client_dashboard.php');
                break;
            case 'agent':
                header('Location: agent_dashboard.php');
                break;
            case 'worker':
                header('Location: worker_dashboard.php');
                break;

            case 'admin':
                header('Location: admin_dashboard.php');
                break;

            case 'transportation':
                if ($user['status'] !== 'approved') {
                    session_unset();
                    session_destroy();
                    echo "<p>Your transportation request is pending. Please wait for approval.</p>";
                    exit;
                }
                $_SESSION['accepted_by'] = $user['accepted_by'];
                header('Location: transportation_dashboard.php');
                exit;
                break;

            default:
                header('Location: login.php');

        }
    } else {
        echo "<script>alert('Invalid credentials or role mismatch!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RuralLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('log.jpeg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-container {
            position: absolute;
            top: 20px;
            bottom: 0px;
            width: 23%;
            height: 54%;
            background: url(public/assets/images/auth-bg-image.jpeg) no-repeat center;
            background-size: contain;
            z-index: 1;
            border-radius: 12px;
        }


        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            z-index: 2;
            margin-top: 10%;
            position: relative;
        }

        .login-container h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }

        .input-group i {
            margin-right: 10px;
            color: #4CAF50;
        }

        .input-group input,
        .input-group select {
            border: none;
            outline: none;
            flex: 1;
            font-size: 1rem;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .links {
            text-align: center;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .links a {
            color: #4CAF50;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="image-container"></div>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="input-group">
                <i class="fas fa-user-tag"></i>
                <select name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="client">Client</option>
                    <option value="agent">Agent</option>
                    <option value="worker">Worker</option>
                    <option value="admin">Admin</option>
                    <option value="transportation">transportation</option>
                </select>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="links">
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
        </div>
    </div>
</body>

</html>