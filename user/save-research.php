<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$researchId = $data['research_id'] ?? null;
$title = $data['title'] ?? '';
$authors = $data['authors'] ?? '';
$abstract = $data['abstract'] ?? '';
$publishedDate = $data['published_date'] ?? null;
$conference = $data['conference'] ?? '';
$paperLink = $data['paper_link'] ?? '';
$userId = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

try {
    if ($researchId) {
        // Update existing research
        $stmt = $conn->prepare("UPDATE research_papers SET title = ?, authors = ?, abstract = ?, published_date = ?, conference = ?, paper_link = ? WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$title, $authors, $abstract, $publishedDate, $conference, $paperLink, $researchId, $userId]);
        $message = 'Research paper updated successfully';
    } else {
        // Create new research
        $stmt = $conn->prepare("INSERT INTO research_papers (user_id, title, authors, abstract, published_date, conference, paper_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $title, $authors, $abstract, $publishedDate, $conference, $paperLink]);
        $message = 'Research paper created successfully';
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