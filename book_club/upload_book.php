<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle file upload
if (isset($_POST['upload_book']) && isset($_FILES['book_file'])) {
    $book_id = $_POST['book_id'] ?? null;

    if (!$book_id) {
        $message = "Please select a book.";
    } else {
        $file_name = $_FILES['book_file']['name'];
        $file_tmp = $_FILES['book_file']['tmp_name'];
        $upload_dir = "uploads/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $destination = $upload_dir . basename($file_name);

        if (move_uploaded_file($file_tmp, $destination)) {
            // Save file info to DB
            $stmt = $conn->prepare("UPDATE books SET file_path = ? WHERE id = ?");
            $stmt->bind_param("si", $destination, $book_id);
            $stmt->execute();

            $message = "📘 File uploaded and linked to book successfully.";
        } else {
            $message = "❌ File upload failed.";
        }
    }
}

// Fetch books for dropdown
$club_id = $_SESSION['club_id'];
$books = $conn->query("SELECT id, title FROM books WHERE club_id = $club_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Book File</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f4f4f9;
      padding: 40px;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
    select, input[type="file"], button {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      background: #2f2f4f;
      color: white;
      border: none;
      cursor: pointer;
      margin-top: 20px;
    }
    button:hover {
      background: #444466;
    }
    .message {
      margin-top: 15px;
      color: green;
      font-weight: bold;
    }
    .back {
      margin-top: 20px;
      display: block;
    }
    .back a {
      text-decoration: none;
      color: #2f2f4f;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📤 Upload Book File</h2>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <form action="upload_book.php" method="POST" enctype="multipart/form-data">
    <label>Select Book:</label>
    <select name="book_id" required>
      <option value="">-- Choose Book --</option>
      <?php while ($book = $books->fetch_assoc()): ?>
        <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Choose File (PDF/DOCX):</label>
    <input type="file" name="book_file" accept=".pdf,.doc,.docx" required>

    <button type="submit" name="upload_book">Upload File</button>
  </form>

  <div class="back">
    <a href="admin_panel.php">← Back to Admin Panel</a>
  </div>
</div>
<!-- Toast Notification -->
<div id="toast" class="toast">File uploaded successfully!</div>

<style>
.toast {
  visibility: hidden;
  background-color: #28a745;
  color: #fff;
  text-align: center;
  padding: 10px;
  border-radius: 5px;
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 1;
}
.toast.show {
  visibility: visible;
  animation: fadein 0.5s, fadeout 0.5s 3s;
}
@keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
@keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
</style>

<script>
function showToast(message) {
  const toast = document.getElementById("toast");
  toast.innerText = message;
  toast.className = "toast show";
  setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 4000);
}
</script>

</body>
</html>
