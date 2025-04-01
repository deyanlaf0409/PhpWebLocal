<?php
session_start();

// Set cache control headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json');

// Include database connection
include '../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Request method is POST. User ID: " . $_POST['user_id'];
} else {
    echo "Not a POST request";
}

// Validate session or token
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id']; // Ensure the app sends the user_id securely (e.g., via token authentication)

    if (!$user_id) {
        echo json_encode(['status' => 'failure', 'message' => 'User ID is required']);
        exit;
    }

    // Fetch the latest user data
    $sqlUser = "SELECT id, username, email FROM users WHERE id = $1";
    $resultUser = pg_query_params($conn, $sqlUser, array($user_id));

    if (pg_num_rows($resultUser) > 0) {
        $user_row = pg_fetch_assoc($resultUser);

        // Fetch notes along with folder_id for the user
        $sqlNotes = "SELECT note_id, text, body, date_created, date_modified, highlighted, folder_id, locked, media, shared FROM data WHERE user_id = $1";
        $resultNotes = pg_query_params($conn, $sqlNotes, array($user_id));

        $notes = [];
        while ($note_row = pg_fetch_assoc($resultNotes)) {
            $notes[] = [
                'id' => $note_row['note_id'],
                'text' => $note_row['text'],
                'body' => $note_row['body'],
                'dateCreated' => $note_row['date_created'],
                'dateModified' => $note_row['date_modified'],
                'highlighted' => $note_row['highlighted'] === 't', // Convert to boolean
                'folderId' => $note_row['folder_id'],
                'locked' => $note_row['locked'] === 't',
                'media' => $note_row['media'],
                'shared' => $note_row['shared'] === 't'

            ];
        }

        // Fetch folders for the user
        $sqlFolders = "SELECT id, name FROM folders WHERE user_id = $1";
        $resultFolders = pg_query_params($conn, $sqlFolders, array($user_id));

        $folders = [];
        while ($folder_row = pg_fetch_assoc($resultFolders)) {
            $folders[] = [
                'id' => $folder_row['id'],
                'name' => $folder_row['name']
            ];
        }

        // Return user info, notes, and folders as JSON
        echo json_encode([
            'status' => 'success',
            'user' => [
                'id' => $user_row['id'],
                'username' => $user_row['username'],
                'email' => $user_row['email']
            ],
            'notes' => $notes,
            'folders' => $folders // Include folders in the response
        ]);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'User not found']);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Invalid request method']);
}

pg_close($conn);
?>
