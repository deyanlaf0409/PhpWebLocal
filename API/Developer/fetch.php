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
$folderName = $_GET['folderName'] ?? null; // Get folderName parameter if provided

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

// Build the base SQL query
$sqlNotes = "
    SELECT 
        data.note_id, 
        data.text,
        data.body, 
        data.date_created, 
        data.date_modified, 
        data.highlighted,
        data.locked, 
        folders.name AS folder_name 
    FROM 
        data 
    LEFT JOIN 
        folders ON data.folder_id = folders.id 
    WHERE 
        data.user_id = $1";

// If folderName is provided, add a condition to filter by folder name
if ($folderName) {
    $sqlNotes .= " AND folders.name = $2";
}

// Execute the query with the appropriate parameters
if ($folderName) {
    $resultNotes = pg_query_params($conn, $sqlNotes, array($user_id, $folderName));
} else {
    $resultNotes = pg_query_params($conn, $sqlNotes, array($user_id));
}

$notes = [];
while ($note_row = pg_fetch_assoc($resultNotes)) {
    $notes[] = [
        'id' => $note_row['note_id'],
        'text' => $note_row['text'],
        'body' => $note_row['body'],
        'dateCreated' => $note_row['date_created'],
        'dateModified' => $note_row['date_modified'],
        'highlighted' => $note_row['highlighted'] === 't',
        'locked' => $note_row['locked'] === 't', // Convert to boolean
        'folderName' => $note_row['folder_name'] // Include folder name
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
