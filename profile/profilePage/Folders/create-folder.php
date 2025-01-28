<?php
session_start();
include '../../../conn_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $folderName = trim($data['name']);
    $userId = $_SESSION['id'];

    if (empty($folderName)) {
        echo json_encode(['success' => false, 'error' => 'Folder name is required.']);
        exit;
    }

    try {
        $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

        // Insert the new folder into the database
        $query = $db->prepare('INSERT INTO folders (user_id, name) VALUES (:user_id, :name) RETURNING id');
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->bindParam(':name', $folderName, PDO::PARAM_STR);
        $query->execute();

        // Get the inserted folder ID
        $folderId = $query->fetchColumn();

        echo json_encode(['success' => true, 'folder_id' => $folderId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
