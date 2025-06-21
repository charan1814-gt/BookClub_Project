<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$club_id = $_SESSION['club_id'];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date = isset($_GET['meeting_date']) ? $_GET['meeting_date'] : '';

$books_result = $conn->query("SELECT * FROM books WHERE club_id = $club_id AND (title LIKE '%$search_query%' OR author LIKE '%$search_query%') ORDER BY added_at DESC");
$meetings_result = $conn->query("SELECT * FROM meetings WHERE club_id = $club_id" . ($filter_date ? " AND meeting_date = '$filter_date'" : "") . " ORDER BY meeting_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔍 Search & Filter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5fa;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        h2 {
            color: #2f2f4f;
        }
        form {
            margin-bottom: 30px;
        }
        input[type="text"], input[type="date"] {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }
        button {
            padding: 10px 20px;
            border: none;
            background: #2f2f4f;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }
        section {
            margin-top: 30px;
        }
        ul {
            padding-left: 20px;
        }
        .back {
            margin-top: 20px;
        }
        .back a {
            text-decoration: none;
            color: #2f2f4f;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🔍 Search and Filter</h2>

    <form method="GET">
        <input type="text" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search_query) ?>">
        <input type="date" name="meeting_date" value="<?= htmlspecialchars($filter_date) ?>">
        <button type="submit">Search & Filter</button>
    </form>

    <section>
        <h3>📚 Books</h3>
        <ul>
            <?php if ($books_result && $books_result->num_rows > 0): ?>
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <li><strong><?= htmlspecialchars($book['title']) ?></strong> by <?= htmlspecialchars($book['author']) ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No matching books found.</li>
            <?php endif; ?>
        </ul>
    </section>

    <section>
        <h3>📅 Meetings</h3>
        <ul>
            <?php if ($meetings_result && $meetings_result->num_rows > 0): ?>
                <?php while ($meeting = $meetings_result->fetch_assoc()): ?>
                    <li><?= $meeting['meeting_date'] ?> – <?= htmlspecialchars($meeting['location']) ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No meetings found for selected date.</li>
            <?php endif; ?>
        </ul>
    </section>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
