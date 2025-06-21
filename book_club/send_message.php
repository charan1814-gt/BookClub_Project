<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['club_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'];
$message = trim($_POST['message'] ?? '');

if ($message !== '') {
    $stmt = $conn->prepare("INSERT INTO chat_messages (club_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $club_id, $user_id, $message);
    $stmt->execute();
}
?>
