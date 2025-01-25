<?php
// Assuming you have a PostgreSQL database connection established
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
