<?php
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $club_name = trim($_POST['club_name']);
    $description = trim($_POST['description']);
    $admin_name = trim($_POST['admin_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if ($club_name && $description && $admin_name && $email && $_POST['password']) {

        // Insert club
        $stmt = $conn->prepare("INSERT INTO clubs (club_name, description) VALUES (?, ?)");

        if ($stmt === false) {
            die("Prepare failed for club insert: " . $conn->error);
        }

        $stmt->bind_param("ss", $club_name, $description);
        if ($stmt->execute()) {
            $club_id = $stmt->insert_id;

            // Insert admin as user
            $stmt2 = $conn->prepare("INSERT INTO users (name, email, password, club_id, is_admin) VALUES (?, ?, ?, ?, 1)");

            if ($stmt2 === false) {
                die("Prepare failed for user insert: " . $conn->error);
            }

            $stmt2->bind_param("sssi", $admin_name, $email, $password, $club_id);
            if ($stmt2->execute()) {
                $message = "Club created successfully!";
            } else {
                $message = "Error creating user: " . $stmt2->error;
            }
        } else {
            $message = "Error creating club: " . $stmt->error;
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create a Book Club</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #f4f4f9;
      padding: 40px;
    }
    h2 {
      text-align: center;
      font-family: 'Playfair Display', serif;
    }
    form {
      max-width: 500px;
      margin: 20px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin: 15px 0 5px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      background-color: #2f2f4f;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 25px;
      font-weight: bold;
      width: 100%;
      margin-top: 20px;
      cursor: pointer;
    }
    button:hover {
      background-color: #444466;
    }
    .message {
      text-align: center;
      color: green;
      margin-top: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<h2>Start Your Own Book Club</h2>

<form method="POST" action="">
  <label>Club Name:</label>
  <input type="text" name="club_name" required>

  <label>Description:</label>
  <textarea name="description" required></textarea>

  <label>Your Name:</label>
  <input type="text" name="admin_name" required>

  <label>Email:</label>
  <input type="email" name="email" required>

  <label>Password:</label>
  <input type="password" name="password" required>

  <button type="submit">Create Club</button>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>
</form>

</body>
</html>
