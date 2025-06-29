<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'events_db';
$username = 'root';
$password = '';

// Get form data
$title = $_POST['title'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$image_url = $_POST['image'] ?? '';
$register_link = $_POST['register_link'] ?? '';

// Validate input
if (empty($title) || empty($date) || empty($time) || empty($location) || empty($description) || empty($image_url) || empty($register_link)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute insert statement
    $stmt = $pdo->prepare("INSERT INTO events (title, event_date, event_time, location, description, image_url, register_link) 
                          VALUES (:title, :event_date, :event_time, :location, :description, :image_url, :register_link)");
    
    $stmt->execute([
        ':title' => $title,
        ':event_date' => $date,
        ':event_time' => $time,
        ':location' => $location,
        ':description' => $description,
        ':image_url' => $image_url,
        ':register_link' => $register_link
    ]);

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Event created successfully']);
} catch (PDOException $e) {
    // Handle database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>