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

// Get the user's ID first
$query_user = "SELECT id FROM users WHERE email = $1";
$result_user = pg_query_params($conn, $query_user, array($email));
if (!$result_user || pg_num_rows($result_user) === 0) {
    die("User not found.");
}

$user = pg_fetch_assoc($result_user);
$user_id = $user['id'];

// Delete note images
$query_notes = "SELECT note_id FROM data WHERE user_id = $1";
$result_notes = pg_query_params($conn, $query_notes, array($user_id));

$uploadDirNotes = "/var/www/html/project/uploads/";
while ($row = pg_fetch_assoc($result_notes)) {
    $file_path = $uploadDirNotes . $row['note_id'] . ".png";
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete profile picture
$profilePicPath = "/var/www/html/project/uploads/profilePictures/" . $user_id . ".png";
if (file_exists($profilePicPath)) {
    unlink($profilePicPath);
}

// Delete the user from the database
$query_delete_user = "DELETE FROM users WHERE id = $1";
$result_delete = pg_query_params($conn, $query_delete_user, array($user_id));

if ($result_delete) {
    session_destroy();
    header("Location: ../goodbye.php"); // Redirect to goodbye page
    exit;
} else {
    echo "Error deleting user: " . pg_last_error($conn);
}

pg_close($conn);
ob_end_flush();
?>
