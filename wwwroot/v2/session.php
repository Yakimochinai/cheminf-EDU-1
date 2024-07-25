<?php
session_start();

// Define session timeout
$timeout = isset($GLOBALS['SESSION_TIMEOUT']) ? $GLOBALS['SESSION_TIMEOUT'] : 24 * 60 * 60; // 默认24小时

// Check if the session has timed out
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    // The last request exceeded the timeout period.
    session_unset();     // Clear $_SESSION variables
    session_destroy();   // Destroy session data
    // Restart the session to avoid potential errors
    session_start();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time(); 

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login page
    exit;
}
?>


// Configure timeout as a configurable value: Set the timeout to a value that can be adjusted in the configuration file instead of hard-coding it in the file.
// Improve timeout handling: When cleaning up the session, in addition to session_unset and session_destroy, you can also consider how to handle browser sessions and user experience.
// Add error handling and logging: In some cases, you may need to record errors and logs for session management.