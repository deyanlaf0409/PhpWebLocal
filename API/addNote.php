<?php
header('Content-Type: application/json');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['note_id'], $input['user_id'], $input['text'], $input['dateCreated'], $input['dateModified'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit();
}

$note_id = $input['note_id'];
$user_id = $input['user_id'];
$text = $input['text'];
$dateCreated = $input['dateCreated'];
$dateModified = $input['dateModified'];
$folder_id = isset($input['folderId']) && !empty($input['folderId']) ? strtoupper($input['folderId']) : null;

include '../conn_db.php';
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}


if ($folder_id) {
    $check_folder_query = "SELECT COUNT(*) FROM folders WHERE id = $1";
    $check_folder_result = pg_query_params($conn, $check_folder_query, [$folder_id]);
    
    if ($check_folder_result) {
        $folder_count = pg_fetch_result($check_folder_result, 0, 0);
        if ($folder_count == 0) {
            $folder_id = null; // Folder does not exist, set to NULL
        }
    }
}

$query = "INSERT INTO data (note_id, user_id, text, date_created, date_modified, folder_id) 
          VALUES ($1, $2, $3, $4, $5, $6)";
$params = [$note_id, $user_id, $text, $dateCreated, $dateModified, $folder_id];

$result = pg_query_params($conn, $query, $params);

if ($result) {
    echo json_encode(['message' => 'Note inserted successfully.']);
} else {
    echo json_encode(['error' => 'Error inserting note: ' . pg_last_error($conn)]);
}

pg_close($conn);
?>
