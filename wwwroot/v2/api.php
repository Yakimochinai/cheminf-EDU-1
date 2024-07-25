<?php
// Get API Key from environment variable
$envApiKey = getenv('CUSTOMCONNSTR_MY_API_KEY');

// Retrieve API key from request header
$requestApiKey = isset($_GET['apiKey']) ? $_GET['apiKey'] : '';

header('Content-Type: application/json');

// Validate API key format (example: non-empty and alphanumeric)
if (!preg_match('/^[a-zA-Z0-9]+$/', $requestApiKey)) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Invalid API key format']);
    exit;
}

if ($requestApiKey === $envApiKey) {
    // Sample data response
    $data = ['message' => 'Access granted', 'data' => ['info' => 'This is a secured data example']];
    echo json_encode($data);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(['message' => 'Access denied']);

    // Log unauthorized access attempts
    error_log('Unauthorized access attempt with API key: ' . $requestApiKey);
}
?>



// Enhance input validation: Ensure that the input format of apiKey is legal.
// Remove debug information: Avoid outputting debug information in production environments.
// Add logging: Record failed access attempts for subsequent auditing and debugging.