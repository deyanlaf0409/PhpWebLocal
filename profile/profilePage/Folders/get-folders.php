<?php
session_start();
include '../../../conn_db.php';

header("Content-Type: application/json");

if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['id'];

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $query = $db->prepare('SELECT id, name FROM folders WHERE user_id = :user_id');
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $folders = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!$folders) {
        $folders = []; // Ensure it always returns an array
    }

    echo json_encode($folders);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
