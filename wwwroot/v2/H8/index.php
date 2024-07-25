<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:ui="http://xmlns.jcp.org/jsf/facelets" xmlns:p="http://primefaces.org/ui" xmlns:h="http://java.sun.com/jsf/html">
<head>
    <meta charset="UTF-8">
    <title>Inventory Database</title>
    <style>
        table {
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
        }
        td {
            background-color: #f2f2f2;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            text-align:left;
            font-weight: bold;
            font-size: 25px;
        }
    </style>
</head>
<body id="page_body">
    <div class="row" id="pagetitle">
        <div class="col-md-12">
            <h1>Inventory Database</h1>
        </div>
    </div>

    <form method="post">
        <input type="submit" name="table" value="Chemicals" />
        <input type="submit" name="table" value="Devices" />
        <input type="submit" name="table" value="Materials" />
    </form>

    <?php
    $host = "den1.mysql6.gear.host";
    $dbname = "situation"; // Database name
    $username = "situation"; // Database username
    $password = "cogni88."; // Database password

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Determine the selected table
    $tables = [
        'Chemicals' => [
            'tableName' => 'H8_Chemicals',
            'headers' => "<th>ID</th><th>Name</th><th>Quantity (g)</th><th>Storage Location</th><th>Purchase Price (€)</th><th>Price (€/g)</th><th>Purchase Date</th><th>Expiration Date</th>"
        ],
        'Devices' => [
            'tableName' => 'H8_Devices',
            'headers' => "<th>ID</th><th>Name</th><th>Location</th><th>Type</th><th>Value (€)</th>"
        ],
        'Materials' => [
            'tableName' => 'H8_Materials',
            'headers' => "<th>ID</th><th>Name</th><th>Quantity (pcs)</th><th>Location</th><th>Value (€)</th>"
        ]
    ];

    // Validate and fetch the selected table data
    if (isset($_POST['table']) && array_key_exists($_POST['table'], $tables)) {
        $selectedTable = $_POST['table'];
        $tableInfo = $tables[$selectedTable];
        $tableName = $tableInfo['tableName'];
        $columnHeaders = $tableInfo['headers'];

        // Execute the SQL query
        $stmt = $conn->prepare("SELECT * FROM " . $tableName);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error executing query: " . $conn->error);
        }

        // Build the HTML table
        echo "<div class='row' id='table'>";
        echo "<div class='col-md-12'>";
        echo "<table>";
        echo "<caption>" . htmlspecialchars($selectedTable) . "</caption>";
        echo "<tr>" . $columnHeaders . "</tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }

        echo "</table></div></div>";
        $stmt->close();
    } else {
        echo "<p>Please select a valid table.</p>";
    }

    $conn->close();
    ?>
</body>
</html>

// SQL injection protection: Although directly inserting table names into SQL queries may not involve SQL injection, using prepared statements instead improves security.
// Input validation: The $tables array is used to validate the table name selected by the user to ensure that only predefined table names are allowed.
// Error handling: Added error handling logic when preparing and executing SQL queries; && added htmlspecialchars to prevent XSS attacks.
// Code organization: The table definition and column header information are organized by using the array $tables, making the code more maintainable and extensible.