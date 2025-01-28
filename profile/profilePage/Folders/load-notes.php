<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo "<p>Please log in to view notes.</p>";
    exit();
}

include '../../../conn_db.php';

$user_id = $_SESSION['id'];
$folder_id = $_GET['folder_id'];

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Query to fetch notes
    if ($folder_id === 'all') {
        $query = $db->prepare('SELECT text, date_created, date_modified FROM data WHERE user_id = :user_id ORDER BY date_modified DESC');
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    } else {
        $query = $db->prepare('SELECT text, date_created, date_modified FROM data WHERE user_id = :user_id AND folder_id = :folder_id ORDER BY date_modified DESC');
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindParam(':folder_id', $folder_id, PDO::PARAM_INT);
    }

    $query->execute();
    $notes = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($notes) {
        foreach ($notes as $note) {
            $formattedDateCreated = (new DateTime($note['date_created']))->format('d/m/Y H:i');
            $formattedDateModified = (new DateTime($note['date_modified']))->format('d/m/Y H:i');
            echo "<div class='note'>";
            echo "<p>" . htmlspecialchars($note['text']) . "</p>";
            echo "<small>Created on: " . htmlspecialchars($formattedDateCreated) . "</small>";
            echo "<small>Last modified on: " . htmlspecialchars($formattedDateModified) . "</small>";
            echo "</div>";
        }
    } else {
        echo "<p>No notes found for this folder.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Error fetching notes: " . $e->getMessage() . "</p>";
}
?>
