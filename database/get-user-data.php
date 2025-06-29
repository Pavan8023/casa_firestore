<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'name' => $_SESSION['name'] ?? 'User',
    'email' => $_SESSION['email'] ?? '',
    'profile_pic' => $_SESSION['profile_pic'] ?? 'https://via.placeholder.com/40'
]);
?>