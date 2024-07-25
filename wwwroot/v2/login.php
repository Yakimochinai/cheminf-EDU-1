<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../style.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="container"> <!-- Container for styling -->
    <h2 class="title">Login</h2> <!-- Title class for the heading -->

<?php
// Placeholder for authentication logic
function authenticate($username, $password) {
    $credentials = getenv('CUSTOMCONNSTR_credentials');
    list($valid_username, $valid_password_hash) = explode(';', $credentials);
    
    // Verify the password using password_verify
    return $username === $valid_username && password_verify($password, $valid_password_hash);
}

// Generate a CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify the CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<p>Invalid CSRF token. Please try again.</p>";
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (authenticate($username, $password)) {
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
        // Redirect to a new page
        header("Location: index.php");
        exit;
    } else {
        echo "<p>Login failed. Please try again.</p>";
    }
}
?>

    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
    <br/><br/>
    <div class="links">
        <a class="link" href="../index.php">Main index</a><br/>
    </div>
    <br/><br/>
    <div class="license">MIT Licence - no commercial interest</div>
</div>
</body>
</html>



// Password Hashing: Use hashing to improve security when storing passwords.
// HTML Form Security: Add csrf protection to prevent cross-site request forgery attacks.
