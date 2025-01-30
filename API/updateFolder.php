<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level (E_ALL logs all types of errors)
error_reporting(E_ALL);

// Allow PUT or PATCH request only
if ($_SERVER['REQUEST_METHOD'] != 'PUT' && $_SERVER['REQUEST_METHOD'] != 'PATCH') {
    http_response_code(405);
    echo json_encode(['message' => 'Only PUT or PATCH requests are allowed']);
    exit();
}

// Read the PUT/PATCH request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['folderId']) || !isset($input['name']) || !isset($input['userId'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, folderId, name, and userId are required']);
    exit();
}

$folder_id = $input['folderId'];
$name = $input['name'];
$user_id = $input['userId'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Update the folder name for the specific user in the database
$query = "UPDATE folders SET name = $1 WHERE id = $2 AND user_id = $3";
$result = pg_query_params($conn, $query, [$name, $folder_id, $user_id]);

// Check if the update was successful
if ($result) {
    if (pg_affected_rows($result) > 0) {
        http_response_code(200); // OK
        echo json_encode(['message' => 'Folder updated successfully']);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['message' => 'Folder not found or not owned by the user']);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Error updating folder: ' . pg_last_error($conn)]);
}

// Close the database connection
pg_close($conn);
?>
