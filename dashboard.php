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

$sql_departments = "SELECT DISTINCT department FROM users";
$result_departments = $conn->query($sql_departments);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SESSION['permission'] == 'Admin') {
  $sql = "SELECT id, user, permission, department FROM users";
  $result = $conn->query($sql);
  
  $sql_users = "SELECT id, user FROM users";
  $result_users = $conn->query($sql_users);

  $sql_lessons = "SELECT lesson_id, title FROM lessons";
  $result_lessons = $conn->query($sql_lessons);

  $sql_progress = "SELECT u.id AS user_id, u.user AS username, up.lesson_id, up.watch_time
                   FROM users u
                   JOIN user_progress up ON u.id = up.user_id
                   GROUP BY u.id, up.lesson_id";
  $result_progress = $conn->query($sql_progress);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
</head>
<body>
  <h2>Welcome to the Dashboard</h2>
  <div class="menu">
  <a href="bitkubapi.php">Bitkub API</a>
  <a href="list_lessons.php">Lessons</a>
</div>

  <p>Role: <?php echo $_SESSION['permission']; ?></p>
  <p>Department: <?php echo $_SESSION['department']; ?></p>
<div class="line_sep"></div>  
  <?php if ($_SESSION['permission'] == 'Admin'): ?>
   <div class="menu">
  <a href="add_video_lesson.php">Add Video Lesson</a>
  <a href="add_url_lesson.php">Add URL Lesson</a>
  <a href="add_meeting_lesson.php">Add Meeting Lesson</a>
</div>
    <h3>User List</h3>
    <table>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Department</th>
      </tr>
	  
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo $row['user']; ?></td>
          <td><?php echo $row['permission']; ?></td>
          <td><?php echo $row['department']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
   <div class="line_sep"></div> 
    <h3>Change User Department</h3>
<form action="change_department.php" method="post">
  <label for="user_id">User ID:</label>
  <input type="number" name="user_id" id="user_id" min="1" required>
  <br>
  <label for="username">Username:</label>
  <span id="username">-</span>
  <br>
  
  <label for="new_department">New Department:</label>
  <select name="new_department" required>
    <?php while ($row_department = $result_departments->fetch_assoc()): ?>
      <option value="<?php echo $row_department['department']; ?>"><?php echo $row_department['department']; ?></option>
    <?php endwhile; ?>
  </select>
  <br>
  <input type="submit" value="Change Department">
</form>
<div class="line_sep"></div>
<h3>User Progress</h3>
<form id="progress-form">
  <label for="lesson-dropdown">Lesson Title:</label>
  <select name="lesson-dropdown" id="lesson-dropdown" required>
    <?php while ($row_lesson = $result_lessons->fetch_assoc()): ?>
      <option value="<?php echo $row_lesson['lesson_id']; ?>"><?php echo $row_lesson['title']; ?></option>
	  

    <?php endwhile; ?>
  </select>
  <br>
  <input type="button" value="Show Progress" onclick="getUserProgress()">
</form>

<div id="user-progress-result">
  <!-- The result of the user progress check will be displayed here -->
</div>
<div class="line_sep"></div>
<h3>Disable User Access</h3>
<form action="disable_user_access.php" method="post">
  <label for="disable-user-id">User ID:</label>
  <input type="number" name="disable-user-id" id="disable-user-id" min="1" required>
  <br>
  <label for="lesson_id">Lesson ID:</label>
  <input type="number" name="lesson_id" id="lesson_id" min="1" required>
  <br>
  <input type="submit" value="Disable Access">
</form>
<div class="line_sep"></div>

<?php endif; ?>

<a href="logout.php">Logout</a>

<script>
  document.getElementById('user_id').addEventListener('change', async function() {
    const userId = this.value;
    const response = await fetch('get_username.php?user_id=' + userId);
    const username = await response.text();
    document.getElementById('username').textContent = username || '-';
  });

  async function getUserProgress() {
    const lessonId = document.getElementById('lesson-dropdown').value;
    const response = await fetch(`get_user_progress.php?lesson_id=${lessonId}`);
    const progress = await response.text();
    document.getElementById('user-progress-result').innerHTML = progress;
  }
</script>

</body>
</html>
