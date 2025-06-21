<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$club_id = $_SESSION['club_id'];

// Analytics queries
$book_count = $conn->query("SELECT COUNT(*) as total FROM books WHERE club_id = $club_id")->fetch_assoc()['total'];
$meeting_count = $conn->query("SELECT COUNT(*) as total FROM meetings WHERE club_id = $club_id")->fetch_assoc()['total'];
$member_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE club_id = $club_id")->fetch_assoc()['total'];
$upcoming_meetings = $conn->query("SELECT COUNT(*) as total FROM meetings WHERE club_id = $club_id AND meeting_date >= CURDATE()")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>📊 Club Analytics</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f2f3f7;
      padding: 40px;
    }
    .card {
      background: white;
      padding: 30px;
      border-radius: 15px;
      margin: 20px auto;
      max-width: 600px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      font-size: 24px;
      color: #2f2f4f;
    }
    .stat {
      font-size: 32px;
      color: #4a4a6a;
      margin: 15px 0;
    }
    .back {
      margin-top: 20px;
    }
    a {
      text-decoration: none;
      color: #2f2f4f;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>📊 Club Analytics Dashboard</h2>
    <div class="stat">📚 Total Books: <?= $book_count ?></div>
    <div class="stat">📅 Meetings Scheduled: <?= $meeting_count ?></div>
    <div class="stat">👥 Members: <?= $member_count ?></div>
    <div class="stat">⏳ Upcoming Meetings: <?= $upcoming_meetings ?></div>
    <div class="back">
      <a href="admin_panel.php">← Back to Admin Panel</a>
    </div>
  </div>
</body>
</html>
