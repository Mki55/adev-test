<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['lesson_id'])) {
  $user_id = intval($_POST['user_id']);
  $lesson_id = intval($_POST['lesson_id']);

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "login";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT * FROM disabled_lessons WHERE user_id = ? AND lesson_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $user_id, $lesson_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo 'blocked';
  } else {
    echo 'not_blocked';
  }

  $stmt->close();
  $conn->close();
} else {
  header("Location: list_url_lessons.php");
}
?>
