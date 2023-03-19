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

$sql = "SELECT DISTINCT department FROM users";
$result = $conn->query($sql);
$departments = [];
while ($row = $result->fetch_assoc()) {
  $departments[] = $row['department'];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
  <title>Add Video Lesson</title>
  <script>
    const departments = <?php echo json_encode($departments); ?>;

        function generateDepartmentDropdowns() {
            const numDropdowns = parseInt(document.getElementById('num_departments').value);
            const container = document.getElementById('department_dropdowns');
            container.innerHTML = '';

            for (let i = 0; i < numDropdowns; i++) {
                const select = document.createElement('select');
                select.name = 'department_access[]';

                departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department;
                    option.textContent = department;
                    select.appendChild(option);
                });

                container.appendChild(select);
                container.appendChild(document.createElement('br'));
            }
        }
  </script>
</head>
<body>
  <h2>Add Video Lesson</h2>
  <form action="process_add_video_lesson.php" method="post" enctype="multipart/form-data">
    <label for="title">Lesson Title:</label>
    <input type="text" name="title" required>
    <br>
    <label for="description">Lesson Description:</label>
    <textarea name="description" required></textarea>
    <br>
    <label for="required_watch_minutes">Required Watch Time (minutes):</label>
    <input type="number" name="required_watch_minutes" min="0" required>
    <br>
    <label for="required_watch_seconds">Required Watch Time (seconds):</label>
    <input type="number" name="required_watch_seconds" min="0" max="59" required>
    <br>
    <label for="video">Upload Video:</label>
    <input type="file" name="video" accept="video/*" required>
    <br>
    <label for="num_departments">Number of Departments:</label>
    <input type="number" id="num_departments" name="num_departments" min="1" onchange="generateDepartmentDropdowns()" required><br><br>

    <div id="department_dropdowns"></div>

    <input type="submit" value="Add Video Lesson">
  </form>

  <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
