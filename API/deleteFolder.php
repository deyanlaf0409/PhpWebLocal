<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level
error_reporting(E_ALL);

// Allow only DELETE request
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['message' => 'Only DELETE requests are allowed']);
    exit();
}

// Read the DELETE request body (this could also be URL parameters)
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['userId']) || !isset($input['folderId'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, user_id and folder_id are required']);
    exit();
}

$user_id = $input['userId'];
$folder_id = $input['folderId'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Delete folder from the database
$query = "DELETE FROM folders WHERE id = $1 AND user_id = $2";
$result = pg_query_params($conn, $query, [$folder_id, $user_id]);

// Check if deletion was successful
if ($result && pg_affected_rows($result) > 0) {
    http_response_code(200);
    echo json_encode(['message' => "Folder with ID $folder_id deleted successfully for User ID $user_id"]);
} else {
    http_response_code(404); // Not Found if folder doesn't exist or doesn't belong to user
    echo json_encode(['message' => "Folder with ID $folder_id not found or could not be deleted for User ID $user_id"]);
}

// Close the database connection
pg_close($conn);
?>
