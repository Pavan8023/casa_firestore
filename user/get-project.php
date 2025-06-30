<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$projectId = $_GET['id'] ?? 0;
$userId = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ? AND user_id = ?");
$stmt->execute([$projectId, $userId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if ($project) {
    echo json_encode($project);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Project not found']);
}
?>