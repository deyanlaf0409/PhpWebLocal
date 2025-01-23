<?php

session_start();

// Set cache control headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Assuming you have a PostgreSQL database connection established
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

    // Perform the database check using parameterized query to prevent SQL injection
    $sql = "SELECT * FROM USERS WHERE email = $1 AND password = $2 AND is_verified = true";
    $result = pg_query_params($conn, $sql, array($email, $password));

    if (pg_num_rows($result) > 0) {
        // User exists and is verified, perform login actions
        $user_row = pg_fetch_assoc($result);
        $username = $user_row['username'];
        $user_id = $user_row['id'];

        // Fetch all team members (users excluding the logged-in user)
        $sqlTeam = "SELECT id, username, email FROM users WHERE id != $1";
        $resultTeam = pg_query_params($conn, $sqlTeam, array($user_id));

        $team_members = [];
        while ($team_row = pg_fetch_assoc($resultTeam)) {
            $team_members[] = [
                'id' => $team_row['id'],
                'username' => $team_row['username'],
                'email' => $team_row['email'],
            ];
        }
        
        
        // Start session variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['id'] = $user_id;
        $_SESSION['team_members'] = $team_members;


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
        // Check if the user exists but is not verified
        $sqlUnverified = "SELECT * FROM USERS WHERE email = $1 AND password = $2 AND is_verified = false";
        $resultUnverified = pg_query_params($conn, $sqlUnverified, array($email, $password));

        if (pg_num_rows($resultUnverified) > 0) {
            // User exists but is not verified
            echo "unverified";
        } else {
            // User does not exist, is not verified, or login failed
            echo "failure";
        }
    }
} else {
    // Handle non-POST requests if needed
    echo "Invalid request method";
}

pg_close($conn);
?>


