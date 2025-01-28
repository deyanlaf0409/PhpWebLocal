<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo "Unauthorized access.";
    exit();
}

include '../../../conn_db.php';

$user_id = $_SESSION['id'];
$folder_id = $_GET['folder_id'];

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Delete notes associated with the folder
    $deleteNotes = $db->prepare('DELETE FROM data WHERE folder_id = :folder_id AND user_id = :user_id');
    $deleteNotes->bindParam(':folder_id', $folder_id, PDO::PARAM_INT);
    $deleteNotes->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $deleteNotes->execute();

    // Delete the folder
    $deleteFolder = $db->prepare('DELETE FROM folders WHERE id = :folder_id AND user_id = :user_id');
    $deleteFolder->bindParam(':folder_id', $folder_id, PDO::PARAM_INT);
    $deleteFolder->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $deleteFolder->execute();

    echo "Folder and its notes have been successfully deleted.";
} catch (PDOException $e) {
    echo "Error deleting folder: " . $e->getMessage();
}
?>
