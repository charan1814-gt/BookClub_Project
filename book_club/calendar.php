<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$club_id = $_SESSION['club_id'];
$meetings = $conn->query("SELECT meeting_date, location, notes FROM meetings WHERE club_id = $club_id ORDER BY meeting_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Meeting Calendar</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: auto;
    }
    #calendar {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    a.back {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #333;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>📅 Calendar View</h2>
  <div id="calendar"></div>
  
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      events: [
        <?php while ($row = $meetings->fetch_assoc()): ?>
        {
          title: "<?= htmlspecialchars($row['location']) ?>",
          start: "<?= $row['meeting_date'] ?>",
          description: "<?= htmlspecialchars($row['notes']) ?>"
        },
        <?php endwhile; ?>
      ],
      eventClick: function (info) {
        alert(info.event.title + ":\n" + info.event.extendedProps.description);
      }
    });
    calendar.render();
  });
</script>
</body>
</html>
