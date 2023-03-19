<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
  <title>Login</title>
</head>
<body>
  <h2>Login</h2>
  <form action="process_login.php" method="post">
    <label for="user">Username:</label>
    <input type="text" name="user" required>
    <br>
    <label for="pass">Password:</label>
    <input type="password" name="pass" required>
    <br>
    <input type="submit" value="Login">
  </form>
</body>
</html>
