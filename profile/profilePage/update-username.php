<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header("Location: /project/Login/construct.php");
    exit();
}

require '../../conn_db.php';

$newUsername = trim($_POST['new_username']);
$currentUsername = $_SESSION['username'];

if (strlen($newUsername) > 20) {
    echo "Username cannot exceed 20 characters.";
    exit();
}

if ($newUsername === $currentUsername) {
    echo "<script>alert('The new username is the same as the current username. Please choose a different one.'); window.location.href = 'profile-page.php';</script>";
    exit();
}

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Check if the new username already exists
    $checkQuery = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :new_username');
    $checkQuery->bindParam(':new_username', $newUsername, PDO::PARAM_STR);
    $checkQuery->execute();

    $userCount = $checkQuery->fetchColumn();

    if ($userCount > 0) {
        // Username already exists
        echo "<script>alert('This username is already taken. Please choose a different one.'); window.location.href = 'profile-page.php';</script>";
        exit();
    }

    // Update the username
    $updateQuery = $db->prepare('UPDATE users SET username = :new_username WHERE id = :user_id');
    $updateQuery->bindParam(':new_username', $newUsername, PDO::PARAM_STR);
    $updateQuery->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $updateQuery->execute();

    // Update session with the new username
    $_SESSION['username'] = $newUsername;

    header("Location: profile-page.php");
} catch (PDOException $e) {
    echo "<script>alert('Error updating username: " . htmlspecialchars($e->getMessage()) . "'); window.location.href = 'profile-page.php';</script>";
}
?>
