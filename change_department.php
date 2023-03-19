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

$user_id = $_POST['user_id'];
$new_department = $_POST['new_department'];

$sql = "UPDATE users SET department = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_department, $user_id);
$result = $stmt->execute();

if ($result) {
  echo "User department has been updated successfully. <a href='dashboard.php'>Return to Dashboard</a>";
} else {
  echo "Error updating user department. <a href='dashboard.php'>Return to Dashboard</a>";
}

$stmt->close();
$conn->close();
?>
