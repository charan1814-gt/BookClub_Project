<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['club_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'];
$message = "";

// Handle new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO discussions (club_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $club_id, $user_id, $msg);
    if ($stmt->execute()) {
        $message = "Message posted!";
    } else {
        $message = "Failed to post.";
    }
}

// Fetch all discussions in the user's club
$stmt = $conn->prepare("
    SELECT d.message, d.posted_at, u.name 
    FROM discussions d 
    JOIN users u ON d.user_id = u.id 
    WHERE d.club_id = ? 
    ORDER BY d.posted_at DESC
");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Discussions</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f7;
            padding: 30px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .message {
            margin: 10px 0;
            color: green;
            text-align: center;
        }
        form {
            margin-bottom: 30px;
        }
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
        }
        button {
            margin-top: 10px;
            padding: 10px 20px;
            background: #2c2c54;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .discussion {
            background: #f9f9fc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid #2c2c54;
        }
        .discussion .author {
            font-weight: bold;
            color: #2c2c54;
        }
        .discussion .time {
            font-size: 0.9em;
            color: #777;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Club Discussions</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="message"><strong>Post a new message:</strong></label>
        <textarea name="message" id="message" rows="4" required></textarea>
        <button type="submit">Post Message</button>
    </form>

    <hr>

    <h3>Recent Discussions</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="discussion">
            <div class="author"><?= htmlspecialchars($row['name']) ?></div>
            <div class="text"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
            <div class="time"><?= date("M d, Y h:i A", strtotime($row['posted_at'])) ?></div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
