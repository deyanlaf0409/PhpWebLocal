<?php
session_start();
include '../../conn_db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

if (!isset($_GET['note_id'])) {
    echo json_encode(["success" => false, "error" => "No note ID provided"]);
    exit();
}

$note_id = $_GET['note_id'];
$user_id = $_SESSION['id'];

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $query = $db->prepare('SELECT text, body, folder_id, media FROM data WHERE note_id = :note_id AND user_id = :user_id');
    $query->bindParam(':note_id', $note_id, PDO::PARAM_STR);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();

    $note = $query->fetch(PDO::FETCH_ASSOC);

    if ($note) {
        echo json_encode(["success" => true, "text" => $note["text"], "body" => $note["body"], 'folder_id' => $note['folder_id'] ?? null, "media" => $note["media"]]);
    } else {
        echo json_encode(["success" => false, "error" => "Note not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
