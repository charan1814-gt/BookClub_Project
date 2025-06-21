<?php
include 'db.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'];
$is_admin = $_SESSION['is_admin'];
$user_name = $_SESSION['user_name'];

// Fetch club info
$club_result = $conn->query("SELECT * FROM clubs WHERE id = $club_id");
if (!$club_result) {
    die("Failed to fetch club info: " . $conn->error);
}
$club = $club_result->fetch_assoc();

// Fetch books
$books = $conn->query("SELECT * FROM books WHERE club_id = $club_id ORDER BY added_at DESC");
if (!$books) {
    die("Failed to fetch books: " . $conn->error);
}

// Fetch meetings
$meetings = $conn->query("SELECT * FROM meetings WHERE club_id = $club_id ORDER BY meeting_date DESC");
if (!$meetings) {
    die("Failed to fetch meetings: " . $conn->error);
}

// Fetch recent discussions
$discussions = $conn->query("
    SELECT d.message, d.posted_at, u.name 
    FROM discussions d 
    JOIN users u ON d.user_id = u.id 
    WHERE d.club_id = $club_id 
    ORDER BY d.posted_at DESC LIMIT 5
");
if (!$discussions) {
    die("Failed to fetch discussions: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - <?= htmlspecialchars($club['club_name']) ?></title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f9f9ff;
      padding: 40px;
    }
    h1, h2 {
      font-family: 'Playfair Display', serif;
      color: #2f2f4f;
    }
    .container {
      max-width: 1000px;
      margin: auto;
    }
    .section {
      background: white;
      padding: 25px;
      margin-top: 30px;
      border-radius: 15px;
      box-shadow: 0 3px 15px rgba(0,0,0,0.05);
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      margin-bottom: 10px;
    }
    .admin-link {
      text-align: right;
    }
    .admin-link a {
      background: #2f2f4f;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 8px;
    }
    .logout {
      position: absolute;
      right: 40px;
      top: 20px;
    }
    .logout a {
      color: #999;
      text-decoration: none;
      font-weight: bold;
    }
    button {
      background-color: #2C2F7F;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #1a1d5c;
    }
  </style>
</head>
<body>

<div class="logout">
  <a href="logout.php">Logout</a>
</div>

<div class="container">
  <h1>Welcome, <?= htmlspecialchars($user_name) ?>!</h1>
  <h2><?= htmlspecialchars($club['club_name']) ?></h2>
  <p><?= htmlspecialchars($club['description']) ?></p>

  <?php if ($is_admin): ?>
  <div class="admin-link">
    <a href="admin_panel.php">Go to Admin Panel</a>
  </div>
  <?php endif; ?>

  <div class="section">
    <h3>📚 Current Books</h3>
    <ul>
      <?php while ($book = $books->fetch_assoc()): ?>
        <li><strong><?= htmlspecialchars($book['title']) ?></strong> by <?= htmlspecialchars($book['author']) ?></li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="section">
    <h3>📅 Upcoming Meetings</h3>
    <ul>
      <?php while ($meeting = $meetings->fetch_assoc()): ?>
        <li><?= $meeting['meeting_date'] ?> – <?= htmlspecialchars($meeting['location']) ?></li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="section">
    <h3>💬 Recent Discussions</h3>
    <ul>
      <?php while ($d = $discussions->fetch_assoc()): ?>
        <li><strong><?= htmlspecialchars($d['name']) ?></strong>: <?= htmlspecialchars($d['message']) ?> <em>(<?= $d['posted_at'] ?>)</em></li>
      <?php endwhile; ?>
    </ul>
  </div>

  <a href="discussion.php" style="display:inline-block; padding:10px 20px; background:#2c2c54; color:white; border-radius:8px; text-decoration:none; margin-top:20px;">Join Club Discussion</a>

  <div class="section">
    <h3>🔍 Search & Filter</h3>
    <p>Find books or filter meetings by date.</p>
    <a href="search.php"><button>Open Search & Filter</button></a>
  </div>

  <div class="section">
    <h3>💬 Club Chat</h3>
    <p>Start real-time discussions with other members.</p>
    <a href="chat.php"><button>Open Chat Room</button></a>
  </div>

  <div class="section">
    <h3>📅 Calendar View</h3>
    <p>View all scheduled meetings in a calendar format.</p>
    <a href="calendar.php"><button>Open Calendar</button></a>
  </div>
</div>

</body>
</html>
