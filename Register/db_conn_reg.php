<?php
// Assuming you have a PostgreSQL database connection established
error_reporting(E_ALL);
ini_set('display_errors', 1);

//var_dump($_POST);


include '../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve registration data from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    //echo "Username: $username, Email: $email, Password: $password";

    // Validate registration data (you may want to add more validation here)

    // Check if the user with the given email already exists
    $sqlCheckUser = "SELECT * FROM USERS WHERE email = $1";
    $resultCheckUser = pg_query_params($conn, $sqlCheckUser, array($email));

    if (pg_num_rows($resultCheckUser) > 0) {
        // User already exists, handle accordingly (e.g., show an error message)
        echo "user_exists";
    } else {
        // User does not exist, proceed with registration
        // You may want to hash the password before storing it in the database for security
        // For demonstration purposes, I'm using a simple INSERT query without password hashing
        $sqlRegister = "INSERT INTO USERS (username, email, password, is_verified, date_added) VALUES ($1, $2, $3, false, CURRENT_DATE)";
        $resultRegister = pg_query_params($conn, $sqlRegister, array($username, $email, $password));

        if ($resultRegister) {
            // Registration successful, perform additional actions if needed
            echo "success";
        } else {
            // Registration failed, handle accordingly (e.g., show an error message)
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
