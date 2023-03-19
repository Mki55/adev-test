<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
}

$lesson_id = intval($_GET['lesson_id']);

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

$sql = "SELECT * FROM lessons WHERE lesson_id = ? AND lesson_type = 'url_lesson' AND (FIND_IN_SET(?, department_access) > 0 OR department_access = 'all') AND NOT EXISTS (
            SELECT 1 FROM disabled_lessons WHERE user_id = ? AND lesson_id = ?
        )";


$stmt = $conn->prepare($sql);
$stmt->bind_param("isii", $lesson_id, $user_dept, $user_id, $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  header("Location: list_url_lessons.php");
  exit;
}
$lesson = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
  <title><?php echo $lesson['title']; ?></title>
  <script>
    setInterval(sendProgressUpdate, 120000);
    setInterval(checkDisabledLesson, 60000);

    function checkDisabledLesson() {
      const lessonId = <?php echo $lesson['lesson_id']; ?>;
      const userId = <?php echo $_SESSION['user_id']; ?>;
      
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "check_disabled_lesson.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
          if (this.responseText === 'blocked') {
            location.reload();
          }
        }
      };
      xhr.send(`user_id=${userId}&lesson_id=${lessonId}`);
    }
	
    function sendProgressUpdate() {
      const lessonId = <?php echo $lesson['lesson_id']; ?>;
      const userId = <?php echo $_SESSION['user_id']; ?>;
      const now = new Date();
	  const clocking = 10;

      const startTime = now.toISOString().slice(0, 19).replace('T', ' ');
      const lastActivity = formatDate(now);

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_progress_url.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
		  console.log("Progress update successful.");
        }
      };
      xhr.send(`user_id=${userId}&lesson_id=${lessonId}&watch_time=${clocking}&last_activity=${lastActivity}`);
    }

    function formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      const seconds = String(date.getSeconds()).padStart(2, '0');
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

  </script>
</head>
<body>
  <h1><?php echo $lesson['title']; ?></h1>
  <p>Description: <?php echo $lesson['description']; ?></p>
  <p>Content:</p>
  <p><?php echo $lesson['Content']; ?></p>
  <p>Due date: <?php echo date('Y-m-d H:i:s', strtotime($lesson['due_date'])); ?></p>
  <a href="list_url_lessons.php">Back to URL Lessons</a>
</body>
</html>
