<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agent') {
    header("Location: login.php");
    exit;
}

// Fetch Open transportation
$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM users WHERE status = 'pending' AND role = 'transportation' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ?)");
$search_term = "%$search%";
$stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
$stmt->execute();
$transportations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle Job Acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_trans'])) {
    $trans_id = $_POST['trans_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 'approved', accepted_by = ? WHERE id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $trans_id);
    $stmt->execute();
    $stmt->close();
    echo "Transportation accepted successfully!";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Pending Transportations</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #45a049;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        /* Job Search */
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-bar button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }

        /* Job Listings */
        .job-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .job-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .job-item:hover {
            background-color: #f1f1f1;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .job-item strong {
            font-size: 16px;
            color: #333;
        }

        .job-item .actions button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .job-item .actions button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .search-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            max-width: 450px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }


        .search-input {
            flex: 1;
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px 0 0 8px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-input:focus {
            border-color: #4caf50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .search-button {
            padding: 12px 20px;
            font-size: 16px;
            color: white;
            background-color: #4caf50;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .search-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <h1>Available Pending Transportations</h1>

    <form method="GET">
        <div class="search-bar">
            <input class="search-input" type="text" name="search"
                placeholder="Search first name, last name, email, area." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </div>
    </form>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>NID</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transportations as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?></td>
                    <td><?= htmlspecialchars($item['address']) ?></td>
                    <td><?= htmlspecialchars($item['email']) ?></td>
                    <td><?= htmlspecialchars($item['nid']) ?></td>
                    <td><?= htmlspecialchars($item['phone']) ?></td>
                    <td>Pending</td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="trans_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="accept_trans">Accept</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>