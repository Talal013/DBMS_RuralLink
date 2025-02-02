<?php
include 'db.php';
require 'vendor/autoload.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['message'], $data['sender_id'], $data['receiver_id'], $data['channelId'])) {
  $message = htmlspecialchars($data['message']);
  $sender_id = intval($data['sender_id']);
  $receiver_id = intval($data['receiver_id']);
  $channel_name = htmlspecialchars($data['channelId']);
  $sent_at = date('Y-m-d H:i:s');

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, sent_at, channel_name) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $sent_at, $channel_name);
  if ($stmt->execute()) {
    // Pusher setup
    $options = [
      'cluster' => 'ap1',
      'useTLS' => true,
    ];

    $pusher = new Pusher\Pusher(
      '85ef0e1afb803f6df9d7',
      'a91d11c31de428fe64e1',
      '1931344',
      $options
    );

    $data = [
      'message' => $message,
      'sender_id' => $sender_id,
      'receiver_id' => $receiver_id,
      'sent_at' => $sent_at,
    ];

    // Trigger Pusher event
    $pusher->trigger($channel_name, 'new-message', $data);


    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
  }
  $stmt->close();
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>