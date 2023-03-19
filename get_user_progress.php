<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['permission'] != 'Admin') {
  http_response_code(403);
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

$lesson_id = $_GET['lesson_id'];

function update_active_status($lesson_id) {
    global $conn;

    $sql = "UPDATE user_progress
            SET active = IF(TIMESTAMPDIFF(MINUTE, last_activity, NOW()) <= 2, 'Online', 'Offline')
            WHERE lesson_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->close();
}

update_active_status($lesson_id);

$sql = "SELECT u.id AS user_id, u.user AS username, up.lesson_id, l.title, MAX(up.watch_time) AS watch_time, up.status, up.active
        FROM users u
        LEFT JOIN user_progress up ON u.id = up.user_id AND up.lesson_id = ?
        LEFT JOIN lessons l ON l.lesson_id = up.lesson_id
        GROUP BY u.id, u.user, up.lesson_id, l.title, up.status, up.active
        ORDER BY u.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

function update_status($lesson_id) {
    global $conn;

    $sql = "UPDATE user_progress
            SET status = 'Attempted'
            WHERE lesson_id = ? AND watch_time >= 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->close();
}

update_status($lesson_id);

?>
<table>
  <tr>
    <th>User ID</th>
    <th>Username</th>
    <th>Lesson ID</th>
    <th>Title</th>
    <th>Status</th>
	<th>Watch Minutes</th>
    <th>Active</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['user_id']; ?></td>
      <td><?php echo $row['username']; ?></td>
      <td><?php echo $row['lesson_id']; ?></td>
      <td><?php echo $row['title']; ?></td>
	  <td><?php echo $row['status']; ?></td>
	  <td><?php echo number_format($row['watch_time'] / 60, 2); ?></td>
      <td><?php echo $row['active']; ?></td>
    </tr>
  <?php endwhile; ?>
</table>

<?php
$stmt->close();
$conn->close();
?>
