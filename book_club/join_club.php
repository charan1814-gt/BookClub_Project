<?php
include 'db.php';
$message = "";

// Fetch all clubs
$clubs = [];
$result = $conn->query("SELECT id, club_name FROM clubs ORDER BY club_name ASC");
while ($row = $result->fetch_assoc()) {
    $clubs[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $club_id = $_POST['club_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if ($club_id && $name && $email && $_POST['password']) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, club_id, is_admin) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("sssi", $name, $email, $password, $club_id);
        if ($stmt->execute()) {
            $message = "You have successfully joined the club!";
        } else {
            $message = "Error: " . $stmt->error;
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
  <title>Join a Book Club</title>
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
    input, select {
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

<h2>Join a Book Club</h2>

<form method="POST" action="">
  <label>Select Club:</label>
  <select name="club_id" required>
    <option value="">-- Choose a Club --</option>
    <?php foreach ($clubs as $club): ?>
      <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['club_name']) ?></option>
    <?php endforeach; ?>
  </select>

  <label>Your Name:</label>
  <input type="text" name="name" required>

  <label>Email:</label>
  <input type="email" name="email" required>

  <label>Password:</label>
  <input type="password" name="password" required>

  <button type="submit">Join Club</button>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>
</form>

</body>
</html>
