<?php
include 'db.php';
session_start();

if (!isset($_SESSION['club_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$club_id = $_SESSION['club_id'];

$sql = "SELECT m.message, m.timestamp, u.name 
        FROM chat_messages m 
        JOIN users u ON m.user_id = u.id 
        WHERE m.club_id = $club_id 
        ORDER BY m.timestamp DESC 
        LIMIT 20";

$result = $conn->query($sql);

if (!$result) {
    // SQL error handling
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch messages: ' . $conn->error]);
    exit();
}

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
