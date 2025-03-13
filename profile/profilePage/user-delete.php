<?php
ob_start(); // Start output buffering

// Start session only if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../conn_db.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: /project/Login/construct.php");
    exit;
}

// Get the user's email
$email = $_SESSION['email'];

// Connect to the database
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Fix: Use correct table name case
$query_notes = "SELECT note_id FROM data WHERE user_id = (SELECT id FROM users WHERE email = $1)";
$result_notes = pg_query_params($conn, $query_notes, array($email));

if (!$result_notes) {
    die("Error fetching notes: " . pg_last_error($conn));
}

// Fixed absolute path to the uploads directory
$uploadDir = "/var/www/html/project/uploads/";  // Absolute path to uploads directory
echo "Resolved upload directory: $uploadDir<br>"; // Debug: Check resolved directory

while ($row = pg_fetch_assoc($result_notes)) {
    $file_path = $uploadDir . $row['note_id'] . ".png";
    echo "Looking for file: $file_path<br>"; // Debug: Print file path

    if (file_exists($file_path)) {
        unlink($file_path);
    } else {
        echo "File not found: $file_path<br>"; // Debug: File not found message
    }
}

// Delete the user from the database
$query_delete_user = "DELETE FROM users WHERE email = $1";
$result_delete = pg_query_params($conn, $query_delete_user, array($email));

if ($result_delete) {
    session_destroy();
    header("Location: ../goodbye.php"); // Redirect to goodbye page
    exit;
} else {
    echo "Error deleting user: " . pg_last_error($conn); // Error if the user can't be deleted
}

// Close DB connection
pg_close($conn);
ob_end_flush(); // End output buffering and flush any remaining output
?>
