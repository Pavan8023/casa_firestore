<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'events_db';

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get all events
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return events as JSON
    echo json_encode($events);
} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>