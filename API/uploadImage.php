<?php
header('Content-Type: application/json');

// Check if the file was uploaded
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests are allowed']);
    exit();
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded']);
    exit();
}

// Generate a unique file name for the uploaded image
$imageName = generateUUID() . '.png';  // Use the UUID for the image name
$uploadDir = __DIR__ . "/../uploads/";  // Go up one directory to reach 'project/uploads/'  // Correct the path to your uploads directory
$uploadFile = $uploadDir . $imageName;

// Ensure the uploads directory exists and is writable
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to create the uploads directory']);
        exit();
    }
}

// Attempt to move the uploaded file to the uploads directory
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    // Return the URL of the uploaded image
    $imageUrl = 'http://192.168.0.222/project/uploads/' . $imageName;
    echo json_encode(['message' => 'Image uploaded successfully', 'url' => $imageUrl]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}

// Function to generate a UUID (v4)
function generateUUID() {
    // Generate random 16 bytes (128 bits)
    $data = random_bytes(16);

    // Set the version (4) in the appropriate bits of the data
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set the version to 4
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set the variant to RFC 4122

    // Convert the raw binary data to a hexadecimal string
    $hexData = unpack('H*', $data)[1];

    // Format the hex data into the UUID format
    return substr($hexData, 0, 8) . '-' . substr($hexData, 8, 4) . '-' . substr($hexData, 12, 4) . '-' . substr($hexData, 16, 4) . '-' . substr($hexData, 20, 12);
}
?>
