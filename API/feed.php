<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json');

include '../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    echo json_encode(['status' => 'failure', 'message' => 'Invalid request method']);
    exit;
}

// Fetch 15 RANDOM shared notes, any user
$sql = "
    SELECT u.username, d.note_id AS id, d.text, d.body, d.media 
    FROM data d
    INNER JOIN users u ON d.user_id = u.id
    WHERE d.shared = true
    ORDER BY random()
    LIMIT 15
";

$result = pg_query($conn, $sql);

$feed = [];
while ($row = pg_fetch_assoc($result)) {
    $feed[] = [
        'card_id' => $row['id'],
        'username' => $row['username'],
        'title' => $row['text'],
        'body' => $row['body'],
        'media' => $row['media'] // could be null
    ];
}

// Return the feed
echo json_encode([
    'status' => 'success',
    'feed' => $feed
]);

pg_close($conn);
?>
