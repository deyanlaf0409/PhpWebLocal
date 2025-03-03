<?php
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

// Validate noteID from the request
if (!isset($_POST['noteID']) || empty($_POST['noteID'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing noteID']);
    exit();
}

$noteID = $_POST['noteID'];

// Ensure noteID follows a valid UUID format (basic validation)
if (!preg_match('/^[0-9a-fA-F-]{36}$/', $noteID)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid noteID format']);
    exit();
}

// Check if the file is uploaded
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded']);
    exit();
}

// Define the upload directory
$uploadDir = __DIR__ . "/../uploads/";
$uploadFile = $uploadDir . $noteID . ".png";  // Use noteID as file name

// Ensure the uploads directory exists
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create the uploads directory']);
        exit();
    }
}

// Move the uploaded file to the uploads directory
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    // Return the uploaded image URL
    $imageUrl = 'http://192.168.0.222/project/uploads/' . $noteID . ".png";
    echo json_encode(['message' => 'Image uploaded successfully', 'url' => $imageUrl]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}
?>
