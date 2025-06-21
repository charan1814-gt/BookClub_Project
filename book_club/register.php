<?php
include 'db.php';
session_start();

$message = "";

// Simple default club ID
$default_club_id = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, club_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $hashed, $default_club_id);
        if ($stmt->execute()) {
            $message = "Registration successful! You can now log in.";
        } else {
            $message = "Error: Email may already be in use.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f1f3f9;
      padding: 40px;
    }
    .container {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    h2 {
      font-family: 'Playfair Display', serif;
      text-align: center;
      color: #2f2f4f;
    }
    label {
      display: block;
      margin-top: 15px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: #2f2f4f;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      cursor: pointer;
    }
    .message {
      margin-top: 15px;
      color: red;
      text-align: center;
    }
    .login-link {
      text-align: center;
      margin-top: 10px;
    }
    .login-link a {
      color: #2f2f4f;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Join the Club</h2>
  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label>Your Name:</label>
    <input type="text" name="name" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Confirm Password:</label>
    <input type="password" name="confirm" required>

    <button type="submit">Register</button>
  </form>

  <div class="login-link">
    Already have an account? <a href="login.php">Log in</a>
  </div>
</div>

</body>
</html>
