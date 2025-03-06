<?php
header('Content-Type: application/json');

// Optional: Enable error reporting
error_reporting(E_ALL);

// Allow only POST requests
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

// Check if a file is uploaded
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded']);
    exit();
}

// Include database connection
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Define the upload directory
$uploadDir = __DIR__ . "/../uploads/";
$uploadFile = $uploadDir . $noteID . ".png";  // Use noteID as filename

// Ensure the uploads directory exists
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to create the uploads directory']);
    exit();
}

// Move the uploaded file to the uploads directory
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    $imageUrl = 'http://192.168.0.222/project/uploads/' . $noteID . ".png";

    // Update the media column in the database
    $query = "UPDATE data SET media = $1 WHERE note_id = $2";
    $result = pg_query_params($conn, $query, [$imageUrl, $noteID]);

    if ($result) {
        if (pg_affected_rows($result) > 0) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'Image uploaded and database updated', 'url' => $imageUrl]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['message' => 'Image uploaded, but no matching note found in the database']);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Error updating database: ' . pg_last_error($conn)]);
    }
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}

// Close the database connection
pg_close($conn);
?>
