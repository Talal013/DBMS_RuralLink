<?php
include 'db.php';
require __DIR__ . '/vendor/autoload.php';


session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$sender_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC");
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $sender_name = $row['sender_id'] === $sender_id ? "You" : "Agent";
    echo "<div><strong>{$sender_name}:</strong> {$row['message']} <span>{$row['sent_at']}</span></div>";
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat_with_id = $_POST['chat_with'];
    $channel_name = "chat_channel_" . min($sender_id, $chat_with_id) . "_" . max($sender_id, $chat_with_id);

    $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $chat_with_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $receiver_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
    } else {
        echo "Name not found.";
    }


    $query = "
    SELECT * FROM chat_messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
    OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY sent_at ASC
";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $sender_id, $chat_with_id, $chat_with_id, $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
}



$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($receiver_name) ?></title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }



        .chat-container {
            width: 60%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .message-box {
            max-height: 300px;
            overflow-y: scroll;
            margin-bottom: 20px;
        }

        .message-box div {
            margin: 10px 0;
        }

        .message-box div strong {
            color: #FF6F00;
        }

        #chat-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        #chat-form button {
            background-color: #FF6F00;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #chat-form button:hover {
            background-color: #FF4500;
        }

        .message.sender {
            background-color: #dff0d8;

            text-align: right;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .message.receiver {
            background-color: #f2dede;
            text-align: left;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        #messages {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        #chat-form {
            margin-top: 10px;
        }

        #message-input {
            width: 100%;
            height: 60px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }

        #send-message {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #send-message:hover {
            background-color: #45a049;
            /* Darker green on hover */
        }

        #error-message {
            color: red;
        }

        .message-time {
            font-size: 0.8em;
            color: #777;
            text-align: right;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <h2>Chat with <?= htmlspecialchars($receiver_name) ?></h2>
    <div class="chat-container">
        <div id="chat-box">
            <div id="messages">
                <?php if ($result->num_rows == 0): ?>
                    <div id="no-messages">Start a conversation</div>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="message <?php echo $row['sender_id'] == $sender_id ? 'sender' : 'receiver'; ?>">
                            <?php echo htmlspecialchars($row['message']); ?>
                            <div class="message-time">
                                <?php echo date('h:i A', strtotime($row['sent_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div id="chat-form">
                <input type="hidden" name="receiver_id" id="receiver_id" value="<?= htmlspecialchars($chat_with_id) ?>">
                <input type="hidden" name="sender_id" id="sender_id"
                    value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
                <input type="hidden" name="channel_name" id="channel_name"
                    value="<?= htmlspecialchars($channel_name) ?>">
                <textarea name="message" id="message-input" placeholder="Type your message here..." required></textarea>
                <button id="send-message">Send Message</button>
                <div id="message_error" class="error-message text-center"></div>
            </div>
        </div>



        <script>
            $(document).ready(function () {
                $("#send-message").on("click", function () {

                    if (validateChatForm()) {
                        var message = $("#message-input").val();
                        var authUserId = $("#sender_id").val();
                        var receiverId = $("#receiver_id").val();
                        var channelName = $("#channel_name").val();

                        var csrfToken = $('meta[name="csrf-token"]').attr("content");

                        $.ajax({
                            url: "send-message.php",
                            type: "POST",
                            data: JSON.stringify({
                                message: message,
                                sender_id: authUserId,
                                receiver_id: receiverId,
                                channelId: channelName,
                            }),
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                            },
                            success: function (response) {
                                $("#message-input").val("");
                            },
                            error: function (xhr) {
                                console.error(xhr.responseText);
                            },
                        });
                    }
                });


                // validation message input field
                function validateChatForm() {
                    var messageInput = $("#message-input").val();

                    // Reset error messages and border colors
                    $("#message_error").text("");
                    $("#message-input").css("border-color", "");

                    if (!messageInput) {
                        $("#message_error")
                            .text("Message field is required.")
                            .css("color", "red");
                        $("#message-input").css("border-color", "red");
                    } else if (messageInput.length > 255) {
                        $("#message_error")
                            .text("Message should be maximum 255 characters.")
                            .css("color", "red");
                        $("#message-input").css("border-color", "red");
                    }

                    // Check if there are any errors
                    if ($("#message_error").text()) {
                        return false;
                    }

                    return true;
                }

                Pusher.logToConsole = true;

                var pusher = new Pusher('85ef0e1afb803f6df9d7', {
                    cluster: 'ap1', // Correct cluster
                    encrypted: true,
                });

                var channelName = document.getElementById('channel_name').value;
                var senderId = document.getElementById('sender_id').value;
                var noMessageText = document.getElementById('no-messages');

                var channel = pusher.subscribe(channelName);
                channel.bind('new-message', function (data) {

                    var messageElement = $('<div></div>').text(data.message).addClass('message');


                    if (data.sender_id == senderId) {
                        messageElement.addClass('sender');
                    } else {
                        messageElement.addClass('receiver');
                    }


                    $('#messages').append(messageElement);

                    if (noMessageText) {
                        noMessageText.innerHTML = '';
                    }


                    $('#messages').scrollTop($('#messages')[0].scrollHeight);
                });
            });
        </script>
</body>

</html>