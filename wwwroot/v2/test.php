<?php
require 'session.php'; // Ensure this is the correct path to your session script

// Verify that the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Securely obtain and process usernames
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hello World</title>
    <link rel="stylesheet" href="../style.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container">
    <h2>Hello World!</h2>
    <p>Welcome, <?php echo $username; ?>.</p>
    <div class="links">
        <a href="index.php">Home</a><br>
        <a href="logout.php">Logout</a> <!-- Ensure you have a logout.php to handle session destruction -->
    </div>
    <div class="license">MIT Licence - no commercial interest</div>
</div>
</body>
</html>



// Improved security: Try to minimize what is output directly to the page, especially what is fetched from the session. Using the htmlspecialchars function is a good habit, but it can be handled more explicitly and rigorously.