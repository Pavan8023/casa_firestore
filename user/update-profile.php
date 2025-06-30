<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$membership = $data['membership'] ?? '';
$currentPassword = $data['current_password'] ?? '';
$newPassword = $data['new_password'] ?? '';
$userId = $_SESSION['user_id'];
$loginMethod = $_SESSION['login_method'];

$db = new Database();
$conn = $db->getConnection();

try {
    // Update name and membership type
    $stmt = $conn->prepare("UPDATE users SET name = ?, membership_type = ? WHERE id = ?");
    $result = $stmt->execute([$name, $membership, $userId]);
    
    if ($result) {
        $_SESSION['user_name'] = $name;
        $_SESSION['membership'] = $membership;
    }
    
    // Password update logic
    $passwordUpdated = false;
    if ($newPassword && $currentPassword) {
        if ($loginMethod === 'google') {
            // Google users - update in google_users table
            $stmt = $conn->prepare("SELECT * FROM google_users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $googleUser = $stmt->fetch();
            
            if ($googleUser && password_verify($currentPassword, $googleUser['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE google_users SET password = ? WHERE user_id = ?");
                $stmt->execute([$hashedPassword, $userId]);
                $passwordUpdated = true;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
                exit;
            }
        } else {
            // Email users - update in users table
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
                $passwordUpdated = true;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
                exit;
            }
        }
    }
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Profile updated successfully' . ($passwordUpdated ? ' and password changed' : ''),
        'newName' => $name
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>