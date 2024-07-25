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

// Get the MoleculeID from the query string and validate it
if (isset($_GET['moleculeID']) && is_numeric($_GET['moleculeID'])) {
    $moleculeID = intval($_GET['moleculeID']);

    // Fetch the SMILES string from the database
    $stmt = $conn->prepare("SELECT CanonicalSmileFormat FROM b2_molecules WHERE MoleculeID = ?");
    $stmt->bind_param("i", $moleculeID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Output the SMILES string
        echo htmlspecialchars($row["CanonicalSmileFormat"]);
    } else {
        echo "No results";
    }

    $stmt->close();
} else {
    echo "Invalid MoleculeID";
}

$conn->close();
?>

// Use prepare and bind_param methods to prevent SQL injection.
// $_GET['moleculeID'] is validated.
// Use htmlspecialchars function to process output to avoid XSS attacks.