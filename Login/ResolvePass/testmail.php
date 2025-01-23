<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

// Assuming you have a PostgreSQL database connection established
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include '../../conn_db.php';

// Establish the database connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];

    $query = "SELECT username FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, array($email));

    if ($row = pg_fetch_assoc($result)) {
      $username = $row['username']; // Retrieve the username
    } else {
      echo "Email not found.";
      exit; // Stop if the email does not exist in the database
    }

    // First, delete any existing reset tokens for this user
    $deleteQuery = "DELETE FROM password_reset_tokens WHERE email = $1";
    pg_query_params($conn, $deleteQuery, array($email));

    // Generate a secure token
    $token = bin2hex(random_bytes(32));
    date_default_timezone_set('Europe/Sofia');
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store the token in the password_reset_tokens table
    $insertQuery = "INSERT INTO password_reset_tokens (email, token, expiration) VALUES ($1, $2, $3)";
    pg_query_params($conn, $insertQuery, array($email, $token, $expiration));

        
        $resetLink = "http://192.168.0.222/project/Login/ResolvePass/reset_password.php?token=$token";

        // Send email with the retrieved password
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'reaper.laf@gmail.com';
        $mail->Password   = 'rftz vgeo wlgr biue';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('reaper.laf@gmail.com');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Forgotten Password';
        $mail->addEmbeddedImage('/var/www/html/project/res/footer-logo.jpg', 'logo_cid');
        // HTML body with professional design
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
          <title>Forgotten Password</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              background-color: #f4f4f4;
              padding: 20px;
            }
            .container {
              max-width: 600px;
              margin: 0 auto;
              background-color: #ffffff;
              padding: 40px;
              border-radius: 10px;
              box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
              color: #333333;
            }
            .reset-button {
              display: inline-block;
              background-color: black;
              color: white !important;
              padding: 10px 20px;
              text-decoration: none;
              border-radius: 25px;
            }
            .reset-button:hover {
              background-color: rgb(75, 75, 75);
            }
              img {
                max-width: 100px;
                height: auto;
                border-radius: 25px;
              }
          </style>
        </head>
        <body>
          <div class="container">
            <h2>Forgotten Password</h2>
            <img src="cid:logo_cid" alt="Verification Image">
            <p>Hello ' . htmlspecialchars($username) . ',</p>

            <p>This is the e-mail for changing your existing password.<p>
            <p>Click on the button below to proceed </p>
            <a href="' . htmlspecialchars($resetLink) . '" class="reset-button">Reset Password</a>

            <br>
            
            <p>Best Regards</p>
            <p>(CompanyName)</p>
          </div>
        </body>
        </html>
        ';
        
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
        $mail->send();

        echo "success";
}

?>