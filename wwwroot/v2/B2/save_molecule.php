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

// Get data from the POST request and validate
$moleculeName = isset($_POST['moleculeName']) ? trim($_POST['moleculeName']) : '';
$canonicalSmile = isset($_POST['canonicalSmile']) ? trim($_POST['canonicalSmile']) : '';
$casId = isset($_POST['casId']) ? trim($_POST['casId']) : '';
$formular = isset($_POST['formular']) ? trim($_POST['formular']) : '';

// Basic validation
if (empty($moleculeName) || empty($canonicalSmile) || empty($casId) || empty($formular)) {
    die("All fields are required.");
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO b2_molecules (MoleculeName, CanonicalSmileFormat, CasId, Formular) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $moleculeName, $canonicalSmile, $casId, $formular);

// Execute the query
if ($stmt->execute()) {
    echo "Molecule saved successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

// SQL injection protection: Use prepare and bind_param methods to prevent SQL injection attacks.
// Input validation: Basic validation is performed on the data in the POST request to ensure that all fields are filled in.
// Error handling: Provides detailed error information to help diagnose problems.