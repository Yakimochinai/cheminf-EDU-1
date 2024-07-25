<?php
// Start a session
session_start();

// Clear all session variables
$_SESSION = array();

// If a session cookie exists, delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy Session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>


// Ensure sessions are started and destroyed correctly.