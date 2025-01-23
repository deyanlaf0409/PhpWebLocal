<?php

session_start();

// Set cache control headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and password from the form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Perform the database check to fetch the hashed password and user details
    $sql = "SELECT id, username, password, is_verified FROM USERS WHERE email = $1";
    $result = pg_query_params($conn, $sql, array($email));

    if (pg_num_rows($result) > 0) {
        // User exists, fetch the user details
        $user_row = pg_fetch_assoc($result);
        $hashedPassword = $user_row['password'];
        $isVerified = $user_row['is_verified'];
        $username = $user_row['username'];
        $user_id = $user_row['id'];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            if ($isVerified === 't') { // PostgreSQL stores boolean as 't' for true

                // Start session variables
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $user_id;

                // Check if request is from the app
                if (isset($_POST['AppRequest']) && $_POST['AppRequest'] === 'true') {
                    // Fetch all user notes
                    $sqlNotes = "SELECT note_id, text, date_created, date_modified, highlighted FROM data WHERE user_id = $1";
                    $resultNotes = pg_query_params($conn, $sqlNotes, array($user_id));

                    $notes = [];
                    while ($note_row = pg_fetch_assoc($resultNotes)) {
                        $notes[] = [
                            'id' => $note_row['note_id'],
                            'text' => $note_row['text'],
                            'dateCreated' => $note_row['date_created'],
                            'dateModified' => $note_row['date_modified'],
                            'highlighted' => $note_row['highlighted']
                        ];
                    }

                    // Return JSON response with user info and notes
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'username' => $username,
                        'user_id' => $user_id,
                        'notes' => $notes
                    ]);
                    exit;
                } else {
                    // Normal success response for non-AppRequest
                    echo "success";
                    exit;
                }
            } else {
                // User exists but is not verified
                echo "unverified";
                exit;
            }
        } else {
            // Invalid password
            echo "failure";
            exit;
        }
    } else {
        // User does not exist
        echo "failure";
        exit;
    }
} else {
    // Handle non-POST requests if needed
    echo "Invalid request method";
}

pg_close($conn);
?>
