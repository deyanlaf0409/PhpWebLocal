<?php
// check_email.php

// Assuming you have a database connection established
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email from the form
    $email = $_POST["email"];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM USERS WHERE email = $1";
    $result = pg_prepare($conn, "check_email", $query);
    $result = pg_execute($conn, "check_email", array($email));

    if (pg_num_rows($result) > 0) {
        // Email exists, echo true
        echo "true";
    } else {
        // Email does not exist, echo false
        echo "false";
    }
}
pg_close($conn);
?>


