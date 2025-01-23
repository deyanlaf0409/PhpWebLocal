<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve registration data from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate input fields (add more validations if needed)
    if (empty($username) || empty($email) || empty($password)) {
        echo "missing_fields";
        exit;
    }

    // Check if the user with the given email already exists
    $sqlCheckUser = "SELECT * FROM USERS WHERE email = $1";
    $resultCheckUser = pg_query_params($conn, $sqlCheckUser, array($email));

    if (pg_num_rows($resultCheckUser) > 0) {
        // User already exists, handle accordingly
        echo "user_exists";
    } else {
        // Encrypt the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $sqlRegister = "INSERT INTO USERS (username, email, password, is_verified, date_added) VALUES ($1, $2, $3, false, CURRENT_DATE)";
        $resultRegister = pg_query_params($conn, $sqlRegister, array($username, $email, $hashedPassword));

        if ($resultRegister) {
            // Registration successful
            echo "success";
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
