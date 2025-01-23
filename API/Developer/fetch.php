<?php
session_start();

// Set cache control headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json');

// Include database connection
include '../../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the token is provided via GET request
$token = $_GET['token'] ?? null;

if (!$token) {
    echo json_encode(['status' => 'failure', 'message' => 'Developer token is required']);
    exit;
}

// Validate the token
$sqlToken = "SELECT id, username, email FROM users WHERE token = $1";
$resultToken = pg_query_params($conn, $sqlToken, array($token));

if (pg_num_rows($resultToken) === 0) {
    echo json_encode(['status' => 'failure', 'message' => 'Invalid or expired developer token']);
    exit;
}

// Fetch user details
$user_row = pg_fetch_assoc($resultToken);
$user_id = $user_row['id'];

// Fetch notes for the user
$sqlNotes = "SELECT note_id, text, date_created, date_modified, highlighted FROM data WHERE user_id = $1";
$resultNotes = pg_query_params($conn, $sqlNotes, array($user_id));

$notes = [];
while ($note_row = pg_fetch_assoc($resultNotes)) {
    $notes[] = [
        'id' => $note_row['note_id'],
        'text' => $note_row['text'],
        'dateCreated' => $note_row['date_created'],
        'dateModified' => $note_row['date_modified'],
        'highlighted' => $note_row['highlighted'] === 't' // Convert to boolean
    ];
}

// Return user info and notes as JSON
echo json_encode([
    'status' => 'success',
    'user' => [
        'username' => $user_row['username'],
        'email' => $user_row['email']
    ],
    'notes' => $notes
]);

pg_close($conn);
?>
