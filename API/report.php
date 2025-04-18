<?php
header('Content-Type: application/json');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

// Check if user_id and note_id are provided
if (!isset($input['user_id'], $input['note_id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input: user_id and note_id are required']);
    exit();
}

$user_id = $input['user_id'];
$note_id = $input['note_id'];


// Connect to the database
include '../conn_db.php';
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Prevent reporting the same note more than once
$check_report_query = "SELECT 1 FROM reported WHERE note_id = $1 LIMIT 1";
$check_report_result = pg_query_params($conn, $check_report_query, [$note_id]);

if ($check_report_result && pg_num_rows($check_report_result) > 0) {
    http_response_code(409); // Conflict
    echo json_encode([
        'message' => 'This note has already been reported',
        'debug' => $comparison
    ]);
    pg_close($conn);
    exit();
}


// Check if the user exists
$user_check_query = "SELECT id FROM users WHERE id = $1 LIMIT 1";
$user_check_result = pg_query_params($conn, $user_check_query, [$user_id]);

if (!$user_check_result || pg_num_rows($user_check_result) == 0) {
    http_response_code(404);
    echo json_encode(['message' => 'User not found']);
    exit();
}

// Check if the note exists and get its user_id
$note_check_query = "SELECT user_id FROM data WHERE note_id = $1 LIMIT 1";
$note_check_result = pg_query_params($conn, $note_check_query, [$note_id]);

// Debugging: Print the query result to ensure the note exists
if (!$note_check_result) {
    http_response_code(500);
    echo json_encode(['message' => 'Error executing query: ' . pg_last_error($conn)]);
    exit();
}

if (pg_num_rows($note_check_result) == 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Note not found']);
    exit();
}

$note_row = pg_fetch_assoc($note_check_result);
$note_owner_id = $note_row['user_id'];


if (strtoupper((string)$note_owner_id) === strtoupper((string)$user_id)) {
    http_response_code(400);
    echo json_encode([
        'message' => 'You cannot report your own note'
    ]);
    exit();
}


// Generate the note URL
$note_url = "https://noteblocks.net/uploads/$note_id.png";

// Insert the reported note into the reported_notes table with the URL
$insert_query = "INSERT INTO reported (note_id, url) VALUES ($1, $2)";
$insert_result = pg_query_params($conn, $insert_query, [$note_id, $note_url]);

if ($insert_result) {
    echo json_encode([
        'message' => 'Note reported successfully.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error reporting note: ' . pg_last_error($conn)]);
}

// Close the database connection
pg_close($conn);
?>
