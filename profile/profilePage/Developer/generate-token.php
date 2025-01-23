<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['id'];
$token_length = 32; // Length of the token
$cooldown_period = 30 * 24 * 60 * 60; // 30 days in seconds

try {
    include '../../../conn_db.php';
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Check the last token generation time
    $query = $db->prepare('SELECT token_generated_at FROM users WHERE id = :user_id');
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['token_generated_at']) {
        $last_generated_at = strtotime($result['token_generated_at']);
        $now = time();

        // Check if the cooldown period has passed
        if (($now - $last_generated_at) < $cooldown_period) {
            $remaining_time = $cooldown_period - ($now - $last_generated_at);
            $days = floor($remaining_time / (24 * 60 * 60));
            $hours = floor(($remaining_time % (24 * 60 * 60)) / (60 * 60));
            $minutes = floor(($remaining_time % (60 * 60)) / 60);

            echo json_encode([
                'success' => false,
                'message' => "You can generate a new token in $days days, $hours hours, and $minutes minutes."
            ]);
            exit;
        }
    }

    // Generate a new token
    $token = bin2hex(random_bytes($token_length / 2)); // Half the length since each byte is 2 hex characters

    // Update the user's token and timestamp
    $query = $db->prepare('UPDATE users SET token = :token, token_generated_at = NOW() WHERE id = :user_id');
    $query->bindParam(':token', $token, PDO::PARAM_STR);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();

    echo json_encode(['success' => true, 'token' => $token]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
