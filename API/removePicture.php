<?php
header('Content-Type: application/json');
error_reporting(E_ALL);

// Allow only DELETE requests
if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    http_response_code(405);
    echo json_encode(['message' => 'Only DELETE requests are allowed']);
    exit();
}

// Read the request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['userID']) || empty($input['userID'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing userID']);
    exit();
}

$userID = $input['userID'];

// Include database connection
include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to connect to the database']);
    exit();
}

// Get the profile picture path
$query = "SELECT picture FROM users WHERE id = $1";
$result = pg_query_params($conn, $query, [$userID]);

if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'User not found']);
    exit();
}

$row = pg_fetch_assoc($result);
$picturePath = $row['picture'];

if ($picturePath) {
    // Update the database to remove the picture reference
    $updateQuery = "UPDATE users SET picture = NULL WHERE id = $1";
    $updateResult = pg_query_params($conn, $updateQuery, [$userID]);

    if ($updateResult) {
        $fileToDelete = __DIR__ . "/../uploads/profilePictures/" . basename($picturePath);
        if (file_exists($fileToDelete) && unlink($fileToDelete)) {
            http_response_code(200);
            echo json_encode(['message' => 'Profile picture removed and deleted successfully']);
        } else {
            http_response_code(200);
            echo json_encode(['message' => 'Profile picture removed, but file not found or could not be deleted']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to update database']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'No profile picture found for this user']);
}

pg_close($conn);
?>
