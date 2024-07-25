// h8.php Function: Confirm that the user is logged in (via session.php). && Display the data of the H8 project or perform some operations.
// Assuming that the H8 project is an inventory management system, h8.php may need to perform the following functions: Display data related to inventory. && Provide interfaces for adding, updating, and deleting data.


<?php
session_start();
require 'session.php'; // Include session management file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to access this page.");
}

// Database connection configuration
$host = "den1.mysql6.gear.host";
$dbname = "situation";
$username = "situation";
$password = "cogni88.";

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Determine the action based on the GET parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'view':
        // Display inventory data
        $tableName = isset($_GET['table']) ? $_GET['table'] : 'H8_Chemicals';
        $sql = "SELECT * FROM " . $conn->real_escape_string($tableName);
        $result = $conn->query($sql);

        if ($result) {
            echo "<h2>Table: " . htmlspecialchars($tableName) . "</h2>";
            echo "<table border='1'><tr>";

            // Generate table headers
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";

            // Populate the table with data
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";

            $result->free();
        } else {
            echo "Query failed: " . $conn->error;
        }
        break;

    case 'add':
        // Add new data to the database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $table = isset($_POST['table']) ? $_POST['table'] : 'H8_Chemicals';
            $columns = $_POST['columns'];
            $values = $_POST['values'];

            $columnsString = implode(', ', array_map('mysqli_real_escape_string', explode(',', $columns)));
            $valuesString = "'" . implode("', '", array_map('mysqli_real_escape_string', explode(',', $values))) . "'";

            $sql = "INSERT INTO " . $conn->real_escape_string($table) . " ($columnsString) VALUES ($valuesString)";
            if ($conn->query($sql) === TRUE) {
                echo "New record inserted successfully.";
            } else {
                echo "Insertion failed: " . $conn->error;
            }
        } else {
            echo "<form method='post' action='h8.php?action=add'>
                    <label for='table'>Table:</label>
                    <input type='text' id='table' name='table'><br><br>
                    <label for='columns'>Columns (comma separated):</label>
                    <input type='text' id='columns' name='columns'><br><br>
                    <label for='values'>Values (comma separated):</label>
                    <input type='text' id='values' name='values'><br><br>
                    <input type='submit' value='Add Data'>
                  </form>";
        }
        break;

    default:
        // Display action selection links
        echo "<h2>Select an action</h2>";
        echo "<a href='h8.php?action=view&table=H8_Chemicals'>View Chemicals</a><br>";
        echo "<a href='h8.php?action=view&table=H8_Devices'>View Devices</a><br>";
        echo "<a href='h8.php?action=view&table=H8_Materials'>View Materials</a><br>";
        echo "<a href='h8.php?action=add'>Add Data</a>";
        break;
}

// Close database connection
$conn->close();
?>


// User authentication: Use session_start() and session.php to verify whether the user is logged in.
// Data operation: View data: Dynamically query the database and display data based on the action parameter and table parameter. &&
// Add data: Provide a form to allow users to add new data to the specified table.
// Security measures: Use mysqli_real_escape_string function to prevent SQL injection.
htmlspecialchars function is used to prevent XSS attacks.
// User interaction: Provides a simple user interface to choose to view different tables or add new data.