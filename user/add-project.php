<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $type = htmlspecialchars(trim($_POST['type']));
    $status = htmlspecialchars(trim($_POST['status']));
    $description = htmlspecialchars(trim($_POST['description']));
    $user_id = $_SESSION['user_id'];

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO projects (user_id, title, type, status, description) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$user_id, $title, $type, $status, $description])) {
        echo json_encode(['status' => 'success', 'message' => 'Project added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add project']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>