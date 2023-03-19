<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405);
  exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  exit;
}

$user_id = $_POST['user_id'];
$lesson_id = $_POST['lesson_id'];
$watch_time = $_POST['watch_time'];
$last_activity = $_POST['last_activity'];

$sql = "INSERT INTO user_progress (user_id, lesson_id, watch_time, last_activity)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE watch_time = watch_time + VALUES(watch_time), last_activity = VALUES(last_activity)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiis", $user_id, $lesson_id, $watch_time, $last_activity);
$result = $stmt->execute();

if ($result) {
  http_response_code(200);
} else {
  http_response_code(500);
}

$stmt->close();
$conn->close();
?>
