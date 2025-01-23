<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level (E_ALL logs all types of errors)
error_reporting(E_ALL);

// Allow DELETE request
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['message' => 'Only DELETE requests are allowed']);
    exit();
}

// Read the DELETE request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['note_id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, note_id is required']);
    exit();
}

$note_id = $input['note_id'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Prepare the SQL query to delete the note
$query = "DELETE FROM data WHERE note_id = $1";
$result = pg_query_params($conn, $query, [$note_id]);

if ($result && pg_affected_rows($result) > 0) {
    echo json_encode(['message' => 'Note deleted successfully']);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Failed to delete note']);
}

// Close the database connection
pg_close($conn);
?>
