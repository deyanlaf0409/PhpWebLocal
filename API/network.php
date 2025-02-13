<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json');

include '../conn_db.php'; // Database connection

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? null;
    $user_id = $_POST['user_id'] ?? null; // Authenticated user (sender)
    $target_username = $_POST['target_username'] ?? null; // Username of the target user

    if (!$user_id && $action !== 'list_requests' && $action !== 'count_requests') { 
        echo json_encode(['status' => 'failure', 'message' => 'User ID is required']);
        exit;
    }
    if (($action === 'send_request' || $action === 'accept_request' || $action === 'decline_request' || $action === 'remove_friend') && !$target_username) {
        echo json_encode(['status' => 'failure', 'message' => 'Target Username is required']);
        exit;
    }

    switch ($action) {
        case 'send_request':
            sendFriendRequest($conn, $user_id, $target_username);
            break;
        case 'accept_request':
        case 'decline_request':
        case 'remove_friend':
            $target_id = getUserIdByUsername($conn, $target_username);
            if (!$target_id) {
                echo json_encode(['status' => 'failure', 'message' => 'User not found']);
                exit;
            }
            if ($action === 'accept_request') acceptFriendRequest($conn, $user_id, $target_id);
            if ($action === 'decline_request') declineFriendRequest($conn, $user_id, $target_id);
            if ($action === 'remove_friend') removeFriend($conn, $user_id, $target_id);
            break;
        case 'list_requests':
            listPendingRequests($conn, $user_id);
            break;
        case 'count_requests':
            countPendingRequests($conn, $user_id);
            break;
        case 'list_friends':
            listFriends($conn, $user_id);
            break;
        default:
            echo json_encode(['status' => 'failure', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Invalid request method']);
}

pg_close($conn);

function sendFriendRequest($conn, $user_id, $target_username) {
    // Get target user's ID by username
    $target_id = getUserIdByUsername($conn, $target_username);
    if (!$target_id) {
        echo json_encode(['status' => 'failure', 'message' => 'User not found']);
        return;
    }
    if ($user_id == $target_id) {
        echo json_encode(['status' => 'failure', 'message' => 'You cannot send a friend request to yourself']);
        return;
    }

    // Check if they are already friends
    $checkFriendship = "SELECT 1 FROM friends WHERE (user1_id = $1 AND user2_id = $2) OR (user1_id = $2 AND user2_id = $1)";
    $result = pg_query_params($conn, $checkFriendship, [$user_id, $target_id]);

    if (pg_num_rows($result) > 0) {
        echo json_encode(['status' => 'failure', 'message' => 'Already friends']);
        return;
    }

    // Insert friend request
    $sql = "INSERT INTO friend_requests (sender_id, receiver_id) VALUES ($1, $2) ON CONFLICT DO NOTHING";
    $result = pg_query_params($conn, $sql, [$user_id, $target_id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Friend request sent']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to send request']);
    }
}

function getUserIdByUsername($conn, $username) {
    $sql = "SELECT id FROM users WHERE username = $1";
    $result = pg_query_params($conn, $sql, [$username]);
    $row = pg_fetch_assoc($result);
    return $row ? $row['id'] : null;
}

function acceptFriendRequest($conn, $user_id, $target_id) {
    $sql = "WITH deleted_request AS (
        DELETE FROM friend_requests 
        WHERE (sender_id = $1 AND receiver_id = $2) 
           OR (sender_id = $2 AND receiver_id = $1) 
        RETURNING sender_id, receiver_id
    )
    INSERT INTO friends (user1_id, user2_id) 
    SELECT sender_id, receiver_id FROM deleted_request";
    
    $result = pg_query_params($conn, $sql, [$target_id, $user_id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Friend request accepted']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to accept request']);
    }
}

function declineFriendRequest($conn, $user_id, $target_id) {
    $sql = "DELETE FROM friend_requests WHERE sender_id = $1 AND receiver_id = $2";
    $result = pg_query_params($conn, $sql, [$target_id, $user_id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Friend request declined']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to decline request']);
    }
}

function removeFriend($conn, $user_id, $target_id) {
    $sql = "DELETE FROM friends WHERE (user1_id = $1 AND user2_id = $2) OR (user1_id = $2 AND user2_id = $1)";
    $result = pg_query_params($conn, $sql, [$user_id, $target_id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Friend removed']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to remove friend']);
    }
}

function listPendingRequests($conn, $user_id) {
    $sql = "SELECT u.id, u.username, u.email 
            FROM friend_requests fr 
            JOIN users u ON u.id = fr.sender_id 
            WHERE fr.receiver_id = $1 AND fr.status = 'pending'";
    
    $result = pg_query_params($conn, $sql, [$user_id]);
    $requests = pg_fetch_all($result) ?: [];

    echo json_encode(['status' => 'success', 'pending_requests' => $requests]);
}

function countPendingRequests($conn, $user_id) {
    $sql = "SELECT COUNT(*) AS count
            FROM friend_requests 
            WHERE receiver_id = $1 AND status = 'pending'";
    
    $result = pg_query_params($conn, $sql, [$user_id]);
    $row = pg_fetch_assoc($result);
    
    if ($row) {
        echo json_encode(['status' => 'success', 'count' => (int)$row['count']]);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Unable to count requests']);
    }
}

function listFriends($conn, $user_id) {
    $sql = "SELECT u.id, u.username, u.email 
            FROM friends f 
            JOIN users u ON (u.id = f.user1_id OR u.id = f.user2_id) 
            WHERE $1 IN (f.user1_id, f.user2_id) AND u.id != $1";
    
    $result = pg_query_params($conn, $sql, [$user_id]);
    $friends = pg_fetch_all($result) ?: [];

    echo json_encode(['status' => 'success', 'friends' => $friends]);
}
?>
