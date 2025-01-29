<?php
header('Content-Type: application/json');

// Optional: Set the error reporting level (E_ALL logs all types of errors)
error_reporting(E_ALL);

// Allow POST request only
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

// Read the POST request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['user_id']) || !isset($input['name']) || !isset($input['folder_id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input, user_id, name, and folder_id are required']);
    exit();
}

$user_id = $input['user_id'];
$name = $input['name'];
$folder_id = $input['folder_id'];

// Connect to PostgreSQL database
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Insert the new folder with the provided folder_id into the database
$query = "INSERT INTO folders (id, user_id, name) VALUES ($1, $2, $3)";
$result = pg_query_params($conn, $query, [$folder_id, $user_id, $name]);

// Check if the insertion was successful
if ($result) {
    http_response_code(201); // Created
    echo json_encode(['message' => 'Folder added successfully']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Error inserting folder: ' . pg_last_error($conn)]);
}

// Close the database connection
pg_close($conn);
?>
