<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql_get_user_dept = "SELECT department FROM users WHERE id = ?";
$stmt_get_dept = $conn->prepare($sql_get_user_dept);
$stmt_get_dept->bind_param("i", $user_id);
$stmt_get_dept->execute();
$result_get_dept = $stmt_get_dept->get_result();
$user_dept = $result_get_dept->fetch_assoc()['department'];
$stmt_get_dept->close();


$sql = "SELECT * FROM lessons WHERE lesson_type = 'url_lesson' AND (FIND_IN_SET(?, department_access) > 0 OR department_access = 'all')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_dept);
$stmt->execute();
$result = $stmt->get_result();
$num_rows = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
  <title>Lessons</title>
</head>
<body>
  <h2>Lessons</h2>
  <div class="lesson_style">
  <?php if ($num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <h3><?php echo $row['title']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <?php if ($row['lesson_type'] == 'video'): ?>
          <p>Required watch time: <?php echo $row['required_watch_minutes']; ?> minutes <?php echo $row['required_watch_seconds']; ?> seconds</p>
          <a href="watch_video.php?lesson_id=<?php echo $row['lesson_id']; ?>">Watch Video</a>
        <?php elseif ($row['lesson_type'] == 'url_lesson'): ?>
          <a href="read_url_lesson.php?lesson_id=<?php echo $row['lesson_id']; ?>">View</a>
        <?php endif; ?>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No lessons found.</p>
  <?php endif; ?>
  </div>

  <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
