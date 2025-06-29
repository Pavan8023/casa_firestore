<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Google login
    if (isset($_POST['credential'])) {
        $credential = $_POST['credential'];
        
        // Verify Google token
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);
        $response = file_get_contents($url);
        $userinfo = json_decode($response, true);

        if (isset($userinfo['error'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Google token']);
            exit;
        }

        $google_id = $userinfo['sub'];
        $email = $userinfo['email'];
        $name = $userinfo['name'] ?? $email;
        $profile_pic = $userinfo['picture'] ?? '';

        $db = new Database();
        $conn = $db->getConnection();

        // Check if user exists in google_users table
        $stmt = $conn->prepare("SELECT * FROM google_users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $google_user = $stmt->fetch();

        if ($google_user) {
            // Get user from users table
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$google_user['user_id']]);
            $user = $stmt->fetch();
        } else {
            // Check if user exists in users table by email
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                // Create new user in users table
                $password = bin2hex(random_bytes(8)); // Generate random password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, membership_type) VALUES (?, ?, ?, 'student')");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $user_id = $conn->lastInsertId();
                    
                    // Create user in google_users table
                    $stmt = $conn->prepare("INSERT INTO google_users (user_id, google_id, name, email, profile_pic) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $google_id, $name, $email, $profile_pic]);
                    
                    // Get the newly created user
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create user']);
                    exit;
                }
            } else {
                // Create entry in google_users table for existing user
                $stmt = $conn->prepare("INSERT INTO google_users (user_id, google_id, name, email, profile_pic) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user['id'], $google_id, $name, $email, $profile_pic]);
            }
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'] ?: $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['membership'] = $user['membership_type'] ?? 'student';
        $_SESSION['profile_pic'] = $profile_pic;
        $_SESSION['loggedin'] = true;
        $_SESSION['login_method'] = 'google';
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Google login successful! Redirecting...'
        ]);
    } 
    // Handle email/password login
    else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Valid email is required'
            ]);
            exit;
        }
        
        // Get user from database
        $stmt = $pdo->prepare("SELECT id, name, email, password, membership_type FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['membership'] = $user['membership_type'];
            $_SESSION['loggedin'] = true;
            $_SESSION['login_method'] = 'email';
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful! Redirecting...'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ]);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>