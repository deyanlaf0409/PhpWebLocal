<?php
// Assuming you have a PostgreSQL database connection established
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // CAPTCHA verification
        $captcha = $_POST['g-recaptcha-response'];

        if (!$captcha) {
            echo "captcha_missing"; // User didn't solve the CAPTCHA
            exit;
        }
    
        // Verify CAPTCHA with Google
        $secretKey = "6LdHnRgrAAAAAHXHVnP_Tihb7pOKanJnwjeFgSTJ"; // <-- Put your actual secret key here
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
        $responseData = json_decode($verifyResponse);
    
        if (!$responseData->success) {
            echo "captcha_failed"; // CAPTCHA validation failed
            exit;
        }
    
        // Continue with your registration logic...
    
    // Retrieve registration data from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate registration data (you may want to add more validation here)

    // Check if the user with the given email already exists
    $sqlCheckEmail = "SELECT * FROM USERS WHERE email = $1";
    $resultCheckEmail = pg_query_params($conn, $sqlCheckEmail, array($email));

    // Check if the user with the given username already exists
    $sqlCheckUsername = "SELECT * FROM USERS WHERE username = $1";
    $resultCheckUsername = pg_query_params($conn, $sqlCheckUsername, array($username));

    if (pg_num_rows($resultCheckEmail) > 0) {
        // Email already exists
        echo "email_exists";
    } elseif (pg_num_rows($resultCheckUsername) > 0) {
        // Username already exists
        echo "username_exists";
    } else {
        // User does not exist, proceed with registration
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sqlRegister = "INSERT INTO USERS (username, email, password, is_verified, date_added) VALUES ($1, $2, $3, false, CURRENT_DATE)";
        $resultRegister = pg_query_params($conn, $sqlRegister, array($username, $email, $hashedPassword));

        if ($resultRegister) {
            // Get the ID of the newly inserted user
            $sqlGetUserId = "SELECT id FROM USERS WHERE email = $1";
            $resultGetUserId = pg_query_params($conn, $sqlGetUserId, array($email));
            $user = pg_fetch_assoc($resultGetUserId);
            $userId = $user['id'];

            // Insert the initial 'Inbox' folder for the new user
            $sqlInsertFolder = "INSERT INTO folders (user_id, name) VALUES ($1, 'Inbox')";
            $resultInsertFolder = pg_query_params($conn, $sqlInsertFolder, array($userId));

            if ($resultInsertFolder) {
                // Folder successfully inserted
                echo "success";
            } else {
                // Folder insertion failed
                echo "folder_failure";
            }
        } else {
            // Registration failed
            echo "failure";
        }
    }
} else {
    // Handle non-POST requests if needed
    echo "Invalid request method";
}

// Close the database connection
pg_close($conn);
?>
