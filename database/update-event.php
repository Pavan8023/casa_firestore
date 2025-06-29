<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "events_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . $conn->connect_error
    ]));
}

// Get form data
$id = $_POST['id'] ?? 0;
$title = $_POST['title'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$location = $_POST['location'] ?? '';
$description = $_POST['description'] ?? '';
$image = $_POST['image'] ?? '';
$registration_link = $_POST['registration_link'] ?? '';

// Validate required fields
$required = ['id', 'title', 'date', 'time', 'location', 'description', 'image', 'registration_link'];
foreach ($required as $field) {
    if (empty($$field)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required'
        ]);
        exit;
    }
}

// Prepare and bind
$stmt = $conn->prepare("UPDATE events SET title=?, event_date=?, event_time=?, location=?, description=?, image_url=?, registration_link=? WHERE id=?");
$stmt->bind_param("sssssssi", $title, $date, $time, $location, $description, $image, $registration_link, $id);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Event updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error updating event: ' . $stmt->error
    ]);
}

// Close connections
$stmt->close();
$conn->close();
?>