<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
echo json_encode(['status' => 'success', 'unread_count' => $result['unread_count']]);
$stmt->close();
?>
<script>
    async function fetchNotifications() {
        const response = await fetch('notifications_backend.php');
        const data = await response.json();
        if (data.status === 'success') {
            const badge = document.getElementById('notification-badge');
            badge.textContent = data.unread_count;
            badge.style.display = data.unread_count > 0 ? 'inline-block' : 'none';
        }
    }

    setInterval(fetchNotifications, 5000);
    fetchNotifications();
</script>
