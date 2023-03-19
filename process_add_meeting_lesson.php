<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['permission'] != 'Admin') {
  header("Location: login.php");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$department_access = $_POST['department_access'] ?? '';

if (is_array($department_access)) {
  $department_access = implode(',', $department_access);
}

$url = $_POST['url'] ?? '';
$due_date = $_POST['due_date'] ?? '';

$sql = "INSERT INTO lessons (lesson_type, title, description, department_access, url, due_date) VALUES ('url_meeting', ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $title, $description, $department_access, $url, $due_date);

$result = $stmt->execute();

if ($result) {
  echo "Meeting lesson has been added successfully. <a href='dashboard.php'>Return to Dashboard</a>";
} else {
  echo "Error adding meeting lesson. <a href='dashboard.php'>Return to Dashboard</a>";
}

$stmt->close();
$conn->close();
?>
