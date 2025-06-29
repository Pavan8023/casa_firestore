<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $membership = $_POST['membership'];
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = "Full name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if (!in_array($membership, ['student', 'alumni', 'faculty'])) $errors[] = "Invalid membership type";
    
    if (count($errors) === 0) {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email already registered'
            ]);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, membership_type, created_at) 
                              VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hashedPassword, $membership]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Account created successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Account creation failed'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => implode('<br>', $errors)
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>