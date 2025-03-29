<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level (E_ALL logs all types of errors)
error_reporting(E_ALL);

// Allow PUT request
if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
    http_response_code(405);
    echo json_encode(['message' => 'Only PUT requests are allowed']);
    exit();
}

// Read the PUT request body
$input = json_decode(file_get_contents('php://input'), true);

// Log the entire decoded input for debugging purposes
error_log("Received input JSON: " . var_export($input, true));

// Validate input
if (!isset($input['id']) || !isset($input['text']) || !isset($input['body']) || !isset($input['dateModified'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit();
}

$id = $input['id'];
$text = $input['text'];
$body = $input['body'];
$dateModified = $input['dateModified'];
$highlighted = $input['highlight'];
$folderId = $input['folderId'];
$locked = $input['locked'];

// Debugging logs to check the highlight value and its type
error_log("Highlight Raw Value: " . var_export($highlighted, true));
error_log("Highlight Type: " . gettype($highlighted));

// Ensure highlight is converted to a boolean, just to be safe
$highlighted = $highlighted ? 'true' : 'false';
error_log("Converted Highlight Value: " . $highlighted);

$locked = $locked ? 'true' : 'false';
error_log("Converted locked Value: " . $locked);

// If folderId is empty, set it to NULL
if (empty($folderId)) {
    $folderId = NULL;
}

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Prepare the SQL query to update the note
$query = "UPDATE data SET text = $1, body = $2, date_modified = $3, highlighted = $4, folder_id = $5, locked = $6 WHERE note_id = $7";
$params = [$text, $body, $dateModified, $highlighted, $folderId, $locked, $id];

// Log the parameters to verify the query
error_log("Query Parameters: " . var_export($params, true));

$result = pg_query_params($conn, $query, $params);

if ($result) {
    echo json_encode(['message' => 'Note updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to update the note', 'error' => pg_last_error($conn)]);
}

// Close the database connection
pg_close($conn);
?>
