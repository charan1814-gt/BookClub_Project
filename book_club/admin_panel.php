<?php
include 'db.php';
session_start();

// Admin check
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$club_id = $_SESSION['club_id'];
$message = "";

// Handle Add Book
if (isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);

    if ($title) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, description, club_id, added_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssii", $title, $author, $description, $club_id, $user_id);
            $stmt->execute();
            $message = "Book added successfully!";
        } else {
            $message = "Error adding book: " . $conn->error;
        }
    } else {
        $message = "Title is required.";
    }
}

// Handle Add Meeting
if (isset($_POST['add_meeting'])) {
    $date = $_POST['meeting_date'];
    $location = trim($_POST['location']);
    $notes = trim($_POST['notes']);

    if ($date && $location) {
        $stmt = $conn->prepare("INSERT INTO meetings (club_id, meeting_date, location, notes, created_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isssi", $club_id, $date, $location, $notes, $user_id);
            $stmt->execute();
            $message = "Meeting scheduled!";
        } else {
            $message = "Error scheduling meeting: " . $conn->error;
        }
    } else {
        $message = "Meeting date and location are required.";
    }
}

// Handle Club Deletion
if (isset($_GET['delete_club'])) {
    $delete_id = intval($_GET['delete_club']);
    if ($conn->query("DELETE FROM clubs WHERE id = $delete_id")) {
        $message = "Club deleted successfully!";
    } else {
        $message = "Error deleting club: " . $conn->error;
    }
}

// Fetch data
$books = $conn->query("SELECT * FROM books WHERE club_id = $club_id ORDER BY added_at DESC");
$meetings = $conn->query("SELECT * FROM meetings WHERE club_id = $club_id ORDER BY meeting_date DESC");
$clubs = $conn->query("SELECT * FROM clubs ORDER BY id DESC"); // fixed line
$users = $conn->query("SELECT * FROM users ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f9f9ff;
      padding: 40px;
    }
    h2 {
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
    label {
      display: block;
      margin: 10px 0 5px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 15px;
      padding: 10px 20px;
      background: #2f2f4f;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background: #444466;
    }
    ul {
      padding-left: 20px;
    }
    .message {
      color: green;
      font-weight: bold;
      margin-top: 10px;
    }
    .back {
      margin-top: 20px;
    }
    .back a {
      text-decoration: none;
      color: #2f2f4f;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #eee;
    }
    a.action {
      color: #c00;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📋 Admin Panel</h2>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="section">
    <h3>Add New Book</h3>
    <form method="POST">
      <label>Title:</label>
      <input type="text" name="title" required>

      <label>Author:</label>
      <input type="text" name="author">

      <label>Description:</label>
      <textarea name="description"></textarea>

      <button type="submit" name="add_book">Add Book</button>
    </form>
  </div>

  <div class="section">
    <h3>Schedule Meeting</h3>
    <form method="POST">
      <label>Meeting Date:</label>
      <input type="date" name="meeting_date" required>

      <label>Location:</label>
      <input type="text" name="location" required>

      <label>Notes:</label>
      <textarea name="notes"></textarea>

      <button type="submit" name="add_meeting">Schedule</button>
    </form>
  </div>

  <div class="section">
    <h3>📚 Existing Books</h3>
    <ul>
      <?php while ($book = $books->fetch_assoc()): ?>
        <li><strong><?= htmlspecialchars($book['title']) ?></strong> by <?= htmlspecialchars($book['author']) ?></li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="section">
    <h3>📅 Scheduled Meetings</h3>
    <ul>
      <?php while ($meeting = $meetings->fetch_assoc()): ?>
        <li><?= htmlspecialchars($meeting['meeting_date']) ?> – <?= htmlspecialchars($meeting['location']) ?></li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="section">
    <h3>🏛️ Manage Clubs</h3>
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Description</th><th>Action</th>
      </tr>
      <?php while ($club = $clubs->fetch_assoc()): ?>
        <tr>
          <td><?= $club['id'] ?></td>
          <td><?= htmlspecialchars($club['club_name']) ?></td>
          <td><?= htmlspecialchars($club['description']) ?></td>
          <td>
            <a href="edit_club.php?id=<?= $club['id'] ?>" class="action">Edit</a> |
            <a href="?delete_club=<?= $club['id'] ?>" class="action" onclick="return confirm('Delete this club?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <div class="section">
    <h3>📊 Club Analytics</h3>
    <p><a href="analytics.php">View Club Analytics Dashboard →</a></p>
  </div>

  <div class="section">
    <h3>📂 Upload Book Document</h3>
    <form action="upload_book.php" method="POST" enctype="multipart/form-data">
      <label>Select Book Document (PDF, DOCX, etc):</label>
      <input type="file" name="book_file" required>

      <label>Book Title:</label>
      <input type="text" name="title" required>

      <label>Description:</label>
      <textarea name="description"></textarea>

      <button type="submit" name="upload_book">Upload</button>
    </form>
  </div>

  <div class="back">
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>
