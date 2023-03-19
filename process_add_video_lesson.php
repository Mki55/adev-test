<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['permission'] != 'Admin') {
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

$title = $_POST['title'];
$description = $_POST['description'];
$department_access = $_POST['department_access'] ?? '';

if (is_array($department_access)) {
  $department_access = implode(',', $department_access);
}


$video_name = $_FILES['video']['name'];
$video_tmp_name = $_FILES['video']['tmp_name'];
$upload_dir = "videos/";

if (!is_dir($upload_dir)) {
  mkdir($upload_dir, 0777, true);
}

move_uploaded_file($video_tmp_name, $upload_dir . $video_name);

$required_watch_minutes = $_POST['required_watch_minutes'];
$required_watch_seconds = $_POST['required_watch_seconds'];

$sql = "INSERT INTO lessons (lesson_type, title, description, department_access, video, required_watch_minutes, required_watch_seconds) VALUES ('video', ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $title, $description, $department_access, $video_name, $required_watch_minutes, $required_watch_seconds);


$result = $stmt->execute();

if ($result) {
  echo "Video lesson has been added successfully. <a href='dashboard.php'>Return to Dashboard</a>";
} else {
  echo "Error adding video lesson. <a href='dashboard.php'>Return to Dashboard</a>";
}

$stmt->close();
$conn->close();
?>
