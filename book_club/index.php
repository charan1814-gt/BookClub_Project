<?php
// index.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Club Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Open+Sans&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Open Sans', sans-serif;
      background-color: #9ebf0a;
      color: #331010;
    }

    header {
      background-color: #0e0e3d;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
    }

    nav a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
    }

    .hero {
      background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?fit=crop&w=1600&q=80');
      background-size: cover;
      background-position: center;
      padding: 100px 20px;
      text-align: center;
      color: white;
    }

    .hero h2 {
      font-size: 3rem;
      font-family: 'Playfair Display', serif;
    }

    .hero p {
      font-size: 1.2rem;
      margin: 20px 0;
    }

    .hero a {
      background-color: #ffffffdd;
      color: #2f2f4f;
      padding: 12px 24px;
      margin: 10px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .hero a:hover {
      background-color: #ffffff;
    }

    .section {
      padding: 60px 20px;
      max-width: 1000px;
      margin: auto;
      text-align: center;
    }

    .section h3 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      margin-bottom: 20px;
    }

    footer {
      background-color: #2f2f4f;
      color: white;
      text-align: center;
      padding: 20px;
    }

    @media (max-width: 600px) {
      .hero h2 {
        font-size: 2rem;
      }

      .hero a {
        display: block;
        margin: 10px auto;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>Book Club Manager</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="create_club.php">Start a Club</a>
    <a href="join_club.php">Join a Club</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="hero">
  <h2>Welcome to Your Book Club Hub</h2>
  <p>Start your own club, choose books, schedule meetings, and engage in vibrant discussions.</p>
  <a href="create_club.php">Start a Club</a>
  <a href="join_club.php">Join a Club</a>
</div>

<div class="section">
  <h3>About the System</h3>
  <p>
    Design and develop a Book Club Management System to manage a book club,
    including book selections, meeting schedules, and member discussions.
    Store book details, meeting schedules, and member information.
  </p>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Book Club Manager. All rights reserved.
</footer>

</body>
</html>
