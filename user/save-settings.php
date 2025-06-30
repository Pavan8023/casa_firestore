<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$theme = $data['theme'] ?? 'dark';
$layout = $data['layout'] ?? 'grid';

// Save settings in session
$_SESSION['theme'] = $theme;
$_SESSION['layout'] = $layout;

// You could also save these to the database for persistence
// between sessions if desired

echo json_encode([
    'status' => 'success',
    'theme' => $theme,
    'layout' => $layout
]);
?>