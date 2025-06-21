<?php
include 'db.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, is_admin, club_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $is_admin, $club_id);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['is_admin'] = $is_admin;
            $_SESSION['club_id'] = $club_id;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f0f2f7;
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
    .register-link {
      text-align: center;
      margin-top: 10px;
    }
    .register-link a {
      color: #2f2f4f;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Welcome Back</h2>
  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
  </form>

  <div class="register-link">
    Don’t have an account? <a href="register.php">Register</a>
  </div>
</div>

</body>
</html>
