<?php
// Database connection
$servername = "den1.mysql6.gear.host";
$username = "situation";
$password = "cogni88.";
$dbname = "situation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get MoleculeID from the request and validate it
if (isset($_GET['moleculeID']) && is_numeric($_GET['moleculeID'])) {
    $moleculeID = intval($_GET['moleculeID']);

    // Fetch molecule data from the database
    $stmt = $conn->prepare("SELECT * FROM b2_molecules WHERE MoleculeID = ?");
    $stmt->bind_param("i", $moleculeID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Return all data entries to the client as a JSON string
        echo json_encode($row);
    } else {
        echo json_encode(["message" => "Molecule not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid MoleculeID"]);
}

// Close the database connection
$conn->close();
?>

// Use prepare and bind_param methods to prevent SQL injection.
// Validate $_GET['moleculeID'] (make sure it is a number).
// Provide JSON format error messages, which are more detailed and friendly.