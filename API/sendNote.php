<?php
header('Content-Type: application/json');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id'], $input['text'], $input['dateCreated'], $input['dateModified'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit();
}

$user_id = $input['user_id'];
$text = $input['text'];
$body = isset($input['body']) ? $input['body'] : '';
$dateCreated = $input['dateCreated'];
$dateModified = $input['dateModified'];
$folder_id = null; // Default folder_id to null if "Inbox" folder doesn't exist

include '../conn_db.php';
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Check if the "Inbox" folder exists for the specific user, and get its ID
$folder_query = "SELECT id FROM folders WHERE name = $1 AND user_id = $2 LIMIT 1";
$folder_result = pg_query_params($conn, $folder_query, ['Inbox', $user_id]);

if ($folder_result) {
    $folder_row = pg_fetch_assoc($folder_result);
    if ($folder_row) {
        $folder_id = $folder_row['id']; // Assign folder_id if "Inbox" exists for this user
    }
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Error checking folder: ' . pg_last_error($conn)]);
    exit();
}

// Generate a new UUID for the note and convert it to uppercase
$note_id = strtoupper(pg_fetch_result(pg_query($conn, "SELECT gen_random_uuid()"), 0, 0));

// Insert the new note into the database
$query = "INSERT INTO data (note_id, user_id, text, body, date_created, date_modified, folder_id) 
          VALUES ($1, $2, $3, $4, $5, $6, $7)";
$params = [$note_id, $user_id, $text, $body, $dateCreated, $dateModified, $folder_id];

$result = pg_query_params($conn, $query, $params);

if ($result) {
    echo json_encode(['message' => 'Note inserted successfully.']);
} else {
    echo json_encode(['error' => 'Error inserting note: ' . pg_last_error($conn)]);
}

pg_close($conn);
?>

