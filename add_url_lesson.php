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
    <title>Add URL Lesson</title>
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

        function submitForm() {
            const form = document.getElementById('add_url_lesson_form');
            const formData = new FormData(form);

            fetch('process_add_url_lesson.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });

            return false;
        }
    </script>
</head>
<body>
    <h1>Add URL Lesson</h1>
    <form id="add_url_lesson_form" onsubmit="return submitForm()">
        <label for="lesson_name">Lesson Name:</label>
        <input type="text" id="lesson_name" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" required><br><br>

        <label for="num_departments">Number of Departments:</label>
        <input type="number" id="num_departments" name="num_departments" min="1" onchange="generateDepartmentDropdowns()" required><br><br>

        <div id="department_dropdowns"></div>

        <button type="submit">Add URL Lesson</button>
    </form>
	<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

