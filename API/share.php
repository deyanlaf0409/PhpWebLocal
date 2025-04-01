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
if (!isset($input['noteId'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, noteId is required']);
    exit();
}

$note_id = $input['noteId'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Toggle the "shared" property of the note
$query = "UPDATE data SET shared = NOT shared WHERE note_id = $1 RETURNING shared";
$result = pg_query_params($conn, $query, [$note_id]);

// Check if the update was successful
if ($result) {
    $row = pg_fetch_assoc($result);
    if ($row) {
        http_response_code(200); // OK
        echo json_encode(['message' => 'Shared status toggled successfully', 'shared' => $row['shared']]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['message' => 'Note not found']);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Error toggling shared status: ' . pg_last_error($conn)]);
}

// Close the database connection
pg_close($conn);
?>
