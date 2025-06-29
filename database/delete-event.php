<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'events_db';
$username = 'root';
$password = '';

// Get event ID
$eventId = $_GET['id'] ?? 0;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid event ID']);
    exit;
}

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute delete statement
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id' => $eventId]);

    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Event not found']);
    }
} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>