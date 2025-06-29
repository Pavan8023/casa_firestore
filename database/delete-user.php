<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'events_db';
$username = 'root';
$password = '';

// Get user ID
$userId = $_GET['id'] ?? 0;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit;
}

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute delete statement
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);

    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>