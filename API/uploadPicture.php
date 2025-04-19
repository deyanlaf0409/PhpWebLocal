<?php
header('Content-Type: application/json');
error_reporting(E_ALL);

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

// Validate userID from the request
if (!isset($_POST['userID']) || empty($_POST['userID'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing userID']);
    exit();
}

$userID = $_POST['userID'];

// Basic UUID format validation
if (!preg_match('/^[0-9a-fA-F-]{36}$/', $userID)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid userID format']);
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
$uploadDir = __DIR__ . "/../uploads/profilePictures/";
$uploadFile = $uploadDir . $userID . ".png";

// Ensure the directory exists
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to create the uploads directory']);
    exit();
}

// Move the uploaded file
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    $imageUrl = 'http://192.168.0.222/project/uploads/profilePictures/' . $userID . ".png";

    // Update the picture column for the user
    $query = "UPDATE users SET picture = $1 WHERE id = $2";
    $result = pg_query_params($conn, $query, [$imageUrl, $userID]);

    if ($result) {
        if (pg_affected_rows($result) > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Profile picture uploaded and database updated', 'url' => $imageUrl]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Image uploaded, but user not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error updating database: ' . pg_last_error($conn)]);
    }
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}

pg_close($conn);
?>
