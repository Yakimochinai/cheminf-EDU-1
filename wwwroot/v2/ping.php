<?php
require 'session.php'; // Make sure the path is correct
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>ChemInformatics EDU - Ping Test</title>
</head>
<body>
    <div class="container">
        <div class="php-test">
            <h2>Check DB Connectivity via Ping</h2> <!-- Add a title -->
            <?php
            // Get the database connection string from the environment variable
            $connStr = getenv('CUSTOMCONNSTR_strConn');

            if ($connStr === false) {
                echo "Error: Unable to retrieve connection string from environment variables.";
            } else {
                // Split the connection string into host name, database name, username and password
                list($host, $dbname, $user, $password) = explode(';', $connStr);

                // Execute the ping command and capture the output
                $pingResult = array();
                $returnVar = null;
                exec("ping " . escapeshellarg($host), $pingResult, $returnVar);

                // Check if the ping command is executed successfully
                if ($returnVar === 0) {
                    // Output ping results
                    echo "<h3>Ping Results:</h3>";
                    foreach ($pingResult as $line) {
                        echo htmlspecialchars($line) . "<br>"; // Avoid XSS using htmlspecialchars
                    }
                } else {
                    echo "<p>Error: Ping command failed. Please check the host and try again.</p>";
                }
            }
            ?>
        </div>
        <div class="license">MIT Licence - no commercial interest</div>
    </div>
</body>
</html>


// Ensure the safety of exec command: Use escapeshellarg function to safely handle the parameters of ping command to avoid potential command injection issues.
// Improve comments and code readability: Add detailed comments to improve code readability.
// Handle possible errors: Provide more detailed error information if environment variables or ping command execution fails.