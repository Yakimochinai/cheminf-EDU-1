<?php

$connStr = getenv('CUSTOMCONNSTR_strConn');
list($dbhost, $dbname, $dbusername, $dbpassword) = explode(';', $connStr);

// Start the session
session_start();

// To store messages
$message = '';

// Database connection
$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize or retrieve the currently selected table
if (isset($_POST['table'])) {
    if (in_array($_POST['table'], ['a1_experiments', 'a1_reacts', 'a1_results'])) {
        $_SESSION['tableSelected'] = $_POST['table'];
    } else {
        unset($_SESSION['tableSelected']);
    }
}
$tableSelected = isset($_SESSION['tableSelected']) ? $_SESSION['tableSelected'] : '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_POST['reload'])) {
    // Fetch and display the table data when the page loads or the reload button is pressed
    if ($tableSelected) {
        $sql = "SELECT * FROM " . $tableSelected;
        $result = $conn->query($sql);

        if ($result) {
            $showAllResults = "<div class='table-container'><table border='1'><tr>";

            // Determine which table we're dealing with and set up headers
            if ($tableSelected == 'a1_experiments') {
                $showAllResults .= "<th>Number</th><th>Workflow_ID</th><th>CreateDate</th><th>Cost</th><th>Purpose</th><th>FKEY_USER</th>";
            } elseif ($tableSelected == 'a1_reacts') {
                $showAllResults .= "<th>MoleculeID</th><th>ExperimentID</th><th>Recipe_amount</th>";
            } elseif ($tableSelected == 'a1_results') {
                $showAllResults .= "<th>ExperimentNumber</th><th>Weight</th><th>Yield</th><th>SampleDesc</th><th>QualityTest</th><th>Spectrum_ID</th>";
            }

            $showAllResults .= "</tr>";

            while ($row = $result->fetch_assoc()) {
                $showAllResults .= "<tr>";
                foreach ($row as $value) {
                    $showAllResults .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $showAllResults .= "</tr>";
            }
            $showAllResults .= "</table></div>";
        } else {
            $message = "Error retrieving records: " . $conn->error;
        }
    }

    // Handle the update request
    if (isset($_POST['update'])) {
        $tableToUpdate = $_POST['table_to_update'];
        $attribute = $_POST['attribute'];
        $newValue = $_POST['newValue'];

        if ($tableToUpdate == 'a1_experiments') {
            $number = $_POST['Number'];
            $sql = "UPDATE a1_experiments SET $attribute = ? WHERE Number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newValue, $number);
        } elseif ($tableToUpdate == 'a1_reacts') {
            $experimentID = $_POST['ExperimentID'];
            $moleculeID = $_POST['MoleculeID'];
            $sql = "UPDATE a1_reacts SET Recipe_amount = ? WHERE ExperimentID = ? AND MoleculeID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dii", $newValue, $experimentID, $moleculeID);
        } elseif ($tableToUpdate == 'a1_results') {
            $experimentNumber = $_POST['ExperimentNumber'];
            $sql = "UPDATE a1_results SET $attribute = ? WHERE ExperimentNumber = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newValue, $experimentNumber);
        }

        // Execute the statement
        if ($stmt) {
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $message = "Record updated successfully.";
            } else {
                $message = "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }

    // Handle the insert request
    if (isset($_POST['insert'])) {
        $table = $_POST['table_to_insert'];
        switch ($table) {
            case 'a1_experiments':
                $number = $_POST['Number'];
                $workflowID = $_POST['Workflow_ID'];
                $createDate = $_POST['CreateDate'];
                $cost = $_POST['Cost'];
                $purpose = $_POST['Purpose'];
                $fkeyUser = $_POST['FKEY_USER'];

                $stmt = $conn->prepare("INSERT INTO a1_experiments (Number, Workflow_ID, CreateDate, Cost, Purpose, FKEY_USER) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iisdsi", $number, $workflowID, $createDate, $cost, $purpose, $fkeyUser);
                break;
            case 'a1_reacts':
                $moleculeID = $_POST['MoleculeID'];
                $experimentID = $_POST['ExperimentID'];
                $recipeAmount = $_POST['Recipe_amount'];

                $stmt = $conn->prepare("INSERT INTO a1_reacts (MoleculeID, ExperimentID, Recipe_amount) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $moleculeID, $experimentID, $recipeAmount);
                break;
            case 'a1_results':
                $experimentID = $_POST['ExperimentID'];
                $weight = $_POST['Weight'];
                $yield = $_POST['Yield'];
                $sampleDesc = $_POST['SampleDesc'];
                $qualityTest = $_POST['QualityTest'];
                $spectrumID = $_POST['Spectrum_ID'];

                $stmt = $conn->prepare("INSERT INTO a1_results (ExperimentNumber, Weight, Yield, SampleDesc, QualityTest, Spectrum_ID) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("idisii", $experimentID, $weight, $yield, $sampleDesc, $qualityTest, $spectrumID);
                break;
        }
        if ($stmt) {
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $message = "Record inserted successfully.";
            } else {
                $message = "Error inserting record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }

    // Handle the search request
    if (isset($_POST['search'])) {
        $table = $_POST['table_to_search'];
        $search_column = $_POST['search_column'];
        $search_value = $_POST['search_value'];

        if ($search_column == 'Purpose') {
            $sql = "SELECT * FROM $table WHERE $search_column LIKE ?";
            $search_value = "%$search_value%";
        } else {
            $sql = "SELECT * FROM $table WHERE $search_column = ?";
        }
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $search_value);
        $stmt->execute();
        $result = $stmt->get_result();

        // Start building the HTML table
        $searchResults = "<div class='table-container'><table border='1'><tr>";

        // Add table headers based on the selected table
        if ($table == 'a1_experiments') {
            $searchResults .= "<th>Number</th><th>Workflow_ID</th><th>CreateDate</th><th>Cost</th><th>Purpose</th><th>FKEY_USER</th>";
        } elseif ($table == 'a1_reacts') {
            $searchResults .= "<th>MoleculeID</th><th>ExperimentID</th><th>Recipe_amount</th>";
        } elseif ($table == 'a1_results') {
            $searchResults .= "<th>ExperimentNumber</th><th>Weight</th><th>Yield</th><th>SampleDesc</th><th>QualityTest</th><th>Spectrum_ID</th>";
        }

        $searchResults .= "</tr>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $searchResults .= "<tr>";
                foreach ($row as $value) {
                    $searchResults .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $searchResults .= "</tr>";
            }
        } else {
            $searchResults .= "<tr><td colspan='5'>No results found.</td></tr>"; // Adjust the colspan based on the number of columns
        }

        $searchResults .= "</table></div>";
        $stmt->close();
    }

    // Handle the delete request
    if (isset($_POST['delete'])) {
        $table = $_POST['table_to_delete'];
        $delete_column = $_POST['delete_column'];
        $delete_value = $_POST['delete_value'];

        $sql = "DELETE FROM $table WHERE $delete_column = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $delete_value);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Record deleted successfully.";
        } else {
            $message = "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>


// Database connection: Read the database connection string from the environment variable and parse it to handle the database connection information in a safer way.
// Session management: Use session_start() to start the session so that session variables can be stored and accessed throughout the life cycle of the page.
// Table selection: Add a form to select the table to be operated, and store the selected table in the session.
// Display table data: According to the selected table, execute SQL query and display all data in the table.
// Update records: Add a form and processing logic for updating records in the table, including different fields of different tables.
// Insert records: Add a form and processing logic for inserting new records.
// Search records: Add a search function to query records in a specific table according to the conditions entered by the user and display the results.
// Delete records: Add a delete function to delete records in a specific table according to the conditions entered by the user.
// Form structure and style: Through HTML forms and CSS styles, make the page more friendly and the operation more intuitive.
// Error handling and message display: Add error handling for various database operations and display corresponding messages on the page.