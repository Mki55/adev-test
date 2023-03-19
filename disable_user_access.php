<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['permission'] != 'Admin') {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['disable-user-id']) && isset($_POST['lesson_id'])) {
    $user_id = intval($_POST['disable-user-id']);
    $lesson_id = intval($_POST['lesson_id']);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "login";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO disabled_lessons (user_id, lesson_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $lesson_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?message=success");
    } else {
        header("Location: dashboard.php?message=error");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: dashboard.php");
}
?>
