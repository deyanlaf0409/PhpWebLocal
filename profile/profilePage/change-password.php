<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /project/Login/construct.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../conn_db.php';

    $user_id = $_SESSION['id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    try {
        $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

        // Fetch the hashed password for the logged-in user
        $query = $db->prepare('SELECT password FROM users WHERE id = :user_id');
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($old_password, $user['password'])) {
            // Old password matches, proceed with updating to the new password

            // Hash the new password
            $hashedNewPassword = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_query = $db->prepare('UPDATE users SET password = :new_password WHERE id = :user_id');
            $update_query->bindParam(':new_password', $hashedNewPassword, PDO::PARAM_STR);
            $update_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_query->execute();

            // Redirect with success message
            echo "<script>window.location.href = 'change-password-done.php';</script>";
            exit();
        } else {
            // Old password does not match
            echo "<script>alert('Old password is incorrect.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .back-link {
            top: 100px;
            left: 100px;
            display: inline-block;
            padding: 25px 25px;
            background-color: white;
            text-decoration: none;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            transition: transform 0.3s ease;
        }

        .back-link::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 11px;
            width: 25px;
            height: 25px;
            background-image: url('../../res/arrow-left.png');
            background-size: contain;
            background-repeat: no-repeat;
            transform: translateY(-50%);
        }

        .back-link:hover {
            transform: scale(1.1);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        form {
            margin: 200px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            width: 90%;
            max-width: 400px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        button {
            background: rgb(254, 220, 0);
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        button:hover {
            background-color: rgb(254, 247, 88);
        }

        @media screen and (max-width: 700px) {
            .back-link {
                top: 20px;
                left: 10px;
            }

            form {
                width: 80%; /* Adjust width to 90% for smaller screens */
                max-width: 350px; /* You can adjust the max width as per your preference */
            }

        }
    </style>
    <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
    <link rel="manifest" href="/project/favicons/site.webmanifest" />
    <link rel="stylesheet" href="../../master/footer-style.css">
</head>
<body>
    <div class="custom-arrow">
        <a href="profile-page.php" class="back-link"></a>
    </div>
    <form class="fade-in" method="post" action="change-password.php" onsubmit="return validatePassword()">

        <h2>Change Password</h2>
    
        <div class="form-group">
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
    
        <button type="submit">Update Password</button>
    </form>

    <script>
        function validatePassword() {
            const oldPassword = document.getElementById('old_password').value;
            const newPassword = document.getElementById('new_password').value;

            // Check if new password is at least 8 characters long
            if (newPassword.length < 8) {
                alert('New password must be at least 8 characters long.');
                return false;
            }

            // Check if new password is the same as the old password
            if (newPassword === oldPassword) {
                alert('New password cannot be the same as the old password.');
                return false;
            }

            return true;
        }
    </script>

    <?php include '../../master/footer.php'; ?>
</body>
</html>
