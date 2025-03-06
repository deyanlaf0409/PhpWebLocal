<?php
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);

// Allow only DELETE requests
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['message' => 'Only DELETE requests are allowed']);
    exit();
}

// Read the request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['noteID']) || empty($input['noteID'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing noteID']);
    exit();
}

$noteID = $input['noteID'];

// Include database connection
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Get the file path from the database
$query = "SELECT media FROM data WHERE note_id = $1";
$result = pg_query_params($conn, $query, [$noteID]);

if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Note not found']);
    exit();
}

$row = pg_fetch_assoc($result);
$filePath = $row['media'];

if ($filePath) {
    $fileToDelete = __DIR__ . "/../uploads/" . basename($filePath);

    // Delete the file if it exists
    if (file_exists($fileToDelete) && unlink($fileToDelete)) {
        // Update the database to remove the media path
        $updateQuery = "UPDATE data SET media = NULL WHERE note_id = $1";
        $updateResult = pg_query_params($conn, $updateQuery, [$noteID]);

        if ($updateResult) {
            http_response_code(200);
            echo json_encode(['message' => 'Image deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update database']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to delete image file']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'No media found for this note']);
}

// Close database connection
pg_close($conn);
?>
