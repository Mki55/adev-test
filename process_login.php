<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['user'];
$pass = $_POST['pass'];

$sql = "SELECT id, permission, department FROM users WHERE user = ? AND pass = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  session_start();
  $_SESSION['user_id'] = $row['id'];
  $_SESSION['permission'] = $row['permission'];
  $_SESSION['department'] = $row['department'];
  header("Location: dashboard.php");
} else {
  echo "Invalid username or password. <a href='login.php'>Try again</a>";
}

$stmt->close();
$conn->close();
?>
