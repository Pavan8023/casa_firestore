<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$projectId = $data['project_id'] ?? null;
$title = $data['title'] ?? '';
$type = $data['type'] ?? '';
$status = $data['status'] ?? '';
$description = $data['description'] ?? '';
$userId = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

try {
    if ($projectId) {
        // Update existing project
        $stmt = $conn->prepare("UPDATE projects SET title = ?, type = ?, status = ?, description = ? WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$title, $type, $status, $description, $projectId, $userId]);
        $message = 'Project updated successfully';
    } else {
        // Create new project
        $stmt = $conn->prepare("INSERT INTO projects (user_id, title, type, status, description) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $title, $type, $status, $description]);
        $message = 'Project created successfully';
    }
    
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => $message]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>