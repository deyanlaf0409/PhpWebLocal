<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level (E_ALL logs all types of errors)
error_reporting(E_ALL);

// Allow PUT request
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

// Read the PUT request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['note_id']) || !isset($input['user_id']) || !isset($input['text']) || !isset($input['dateCreated']) || !isset($input['dateModified'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit();
}

$note_id = $input['note_id'];
$user_id = $input['user_id'];
$text = $input['text'];
$dateCreated = $input['dateCreated'];
$dateModified = $input['dateModified'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}


// Assuming $conn is your PostgreSQL connection
$query = "INSERT INTO data (note_id, user_id, text, date_created, date_modified) VALUES ($1, $2, $3, $4, $5)";
$result = pg_query_params($conn, $query, [$note_id, $user_id, $text, $dateCreated, $dateModified]);

// Check if the insertion was successful
if ($result) {
    echo "Note inserted successfully.";
} else {
    echo "Error inserting note: " . pg_last_error($conn);
}


// Close the database connection
pg_close($conn);
?>