<?php
require 'session.php'; // Ensure this is the path to the session script
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>ChemInformatics EDU</title>
</head>
<body>
    <div class="container">
        <div class="php-test">
            Check DB, via environment variables ... <br/>

<?php

// Get connection string from environment variable
$connStr = getenv('CUSTOMCONNSTR_strConn');

// Validate connection string format
if (!$connStr || substr_count($connStr, ';') != 3) {
    die("Invalid connection string format.");
}

list($host, $dbname, $user, $password) = explode(';', $connStr);

// Start session
session_start();

try {
    // Create connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Select example
    $sql = "SELECT * FROM situation.h8_chemicals;";
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "id: " . htmlspecialchars($row["id"]) . " - Name: " . htmlspecialchars($row["name"]) . " - quantity: " . htmlspecialchars($row["quantity"]) . "<br>";
        }
    } else {
        echo "0 results";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}

?>
        </div>
        <div class="license">MIT Licence - no commercial interest</div>
    </div>
</body>
</html>


// Enhanced error handling: Provides more detailed error information.
// Better handling of environment variables: More robust when parsing environment variables.
// SQL injection prevention: Although there is no user input in the current query, in general you should use prepared statements to prevent SQL injection.