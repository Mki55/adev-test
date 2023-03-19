<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM lessons";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Lessons</title>
	  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
</head>
<body>
    <h1>Lessons</h1>
	  <div class="menu">
  <a href="list_url_lessons.php">List of URL Lessons</a>
  <a href="list_video_lessons.php">List of Video Lessons</a>
</div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Due Date</th>
                <th>Access</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                $accessURL = '';

                if ($row['lesson_type'] === 'url_meeting') {
                    $dueDate = new DateTime($row['due_date']);
                    $now = new DateTime();
                    $isAvailable = $now <= $dueDate;

                    if ($isAvailable) {
                        $accessURL = '<a href="' . $row['url'] . '">Access</a>';
                    } else {
                        $accessURL = 'Course closed';
                    }
                } else if ($row['lesson_type'] === 'video') {
                    $accessURL = "<a href=\"watch_video.php?lesson_id={$row['lesson_id']}\">Access</a>";
                }
				  else if ($row['lesson_type'] === 'url_lesson') {
                    $accessURL = "<a href=\"read_url_lesson.php?lesson_id={$row['lesson_id']}\">Access</a>";
                }
				

                echo "<tr>";
                echo "<td>{$row['title']}</td>";
                echo "<td>{$row['due_date']}</td>";
                echo "<td>{$accessURL}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
	<a href="dashboard.php">Back to Dashboard</a>
    <?php
    $result->free();
    $conn->close();
    ?>
</body>
</html>
