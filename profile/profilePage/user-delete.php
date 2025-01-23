<?php
// Start session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../conn_db.php';


// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: /project/Login/construct.php"); // Redirect to login if not authenticated
    exit;
}

// Get the user's email from the session
$email = $_SESSION['email'];

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");


$query = "DELETE FROM Users WHERE email = $1";
    $result = pg_query_params($conn, $query, array($email));
    
    if ($result) {
        // Redirect the user to verification success page
        header("Location: ../goodbye.php");
        exit();
    } else {
        echo "Error updating verification status: " . pg_last_error($conn);
    }

// Execute the statement
if ($stmt->execute()) {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();

    // Redirect to another page after deletion
    header("Location: ../goodbye.php"); // Change this to the page where you want to redirect after deletion
    exit;
} else {
    echo "Error deleting record: " . $conn->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>