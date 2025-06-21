<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Club ID not specified.";
    exit();
}

$club_id = intval($_GET['id']);
$message = "";

// Fetch existing club data
$stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
$stmt->bind_param("i", $club_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Club not found.";
    exit();
}

$club = $result->fetch_assoc();

// Handle update form submission
if (isset($_POST['update_club'])) {
    $club_name = trim($_POST['club_name']);
    $description = trim($_POST['description']);

    if ($club_name) {
        $stmt = $conn->prepare("UPDATE clubs SET club_name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $club_name, $description, $club_id);
        if ($stmt->execute()) {
            $message = "Club updated successfully!";
            // Refresh data
            $club['club_name'] = $club_name;
            $club['description'] = $description;
        } else {
            $message = "Failed to update club: " . $conn->error;
        }
    } else {
        $message = "Club name is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Club</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f9f9ff;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        h2 {
            font-family: 'Playfair Display', serif;
            color: #2f2f4f;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
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
        .message {
            margin-top: 20px;
            color: green;
            font-weight: bold;
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
    <h2>✏️ Edit Club</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Club Name:</label>
        <input type="text" name="club_name" value="<?= htmlspecialchars($club['club_name']) ?>" required>

        <label>Description:</label>
        <textarea name="description"><?= htmlspecialchars($club['description']) ?></textarea>

        <button type="submit" name="update_club">Update Club</button>
    </form>

    <div class="back">
        <a href="admin_panel.php">← Back to Admin Panel</a>
    </div>
</div>

</body>
</html>
