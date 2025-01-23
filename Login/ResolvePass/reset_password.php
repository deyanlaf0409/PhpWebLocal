<?php
use PHPMailer\PHPMailer\Exception;

require '../../conn_db.php';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token
    $query = "SELECT email, expiration FROM password_reset_tokens WHERE token = $1";
    $result = pg_query_params($conn, $query, array($token));
    $row = pg_fetch_assoc($result);

    if ($row) {
        $email = $row['email'];
        $expiration = $row['expiration'];

        // Check if the token has expired
        if (strtotime($expiration) < time()) {
            echo "This token has expired.";
            exit();
        }
    } else {
        echo "Invalid token.";
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side password length validation
    if (strlen($new_password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Validate the token again
        $query = "SELECT email FROM password_reset_tokens WHERE token = $1";
        $result = pg_query_params($conn, $query, array($token));
        $row = pg_fetch_assoc($result);

        if ($row) {
            $email = $row['email'];

            // Hash the new password
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the users table
            $updateQuery = "UPDATE users SET password = $1 WHERE email = $2";
            pg_query_params($conn, $updateQuery, array($hashedPassword, $email));

            // Delete the used token
            $deleteQuery = "DELETE FROM password_reset_tokens WHERE token = $1";
            pg_query_params($conn, $deleteQuery, array($token));

            echo "<script>alert('Password successfully updated.');</script>";
            echo "<script>window.location.href = '/project/Login/construct.php';</script>";
            exit();
        } else {
            echo "Invalid token.";
            exit();
        }
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: rgb(254, 220, 0);
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: rgb(254, 247, 88);
        }
    </style>
</head>
<body>
    <form method="post" action="reset_password.php">
        <h2>Reset Password</h2>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required minlength="8">
        </div>
        <div>
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </div>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
