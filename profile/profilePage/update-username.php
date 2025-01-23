<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id'])) {
    header("Location: /project/Login/construct.php");
    exit();
}

require '../../conn_db.php';

$newUsername = trim($_POST['new_username']);
$currentUsername = $_SESSION['username'];

if ($newUsername === $currentUsername) {
    echo "<script>alert('The new username is the same as the current username. Please choose a different one.'); window.location.href = 'profile-page.php';</script>";
    exit();
}

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    $query = $db->prepare('UPDATE users SET username = :new_username WHERE id = :user_id');
    $query->bindParam(':new_username', $newUsername, PDO::PARAM_STR);
    $query->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();

    // Update session with the new username
    $_SESSION['username'] = $newUsername;

    header("Location: profile-page.php");
} catch (PDOException $e) {
    echo "Error updating username: " . $e->getMessage();
}
?>
