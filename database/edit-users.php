<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "events_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$id = $name = $email = $membership_type = "";
$error = "";

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $membership_type = trim($_POST['membership_type']);
    
    // Validation
    if (empty($name) || empty($email) || empty($membership_type)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Update record
        $sql = "UPDATE users SET name=?, email=?, membership_type=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $membership_type, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("User updated successfully!")</script>';
            exit();
        } else {
            $error = "Error updating record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
} else if (isset($_GET['id'])) {
    // Fetch existing data
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $name = $row['name'];
        $email = $row['email'];
        $membership_type = $row['membership_type'];
    } else {
        die("Record not found");
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        .container { max-width: 500px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], select {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
        }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Membership Type:</label>
                <select name="membership_type" required>
                    <option value="Student" <?php echo ($membership_type == 'Student') ? 'selected' : ''; ?>>Student</option>
                    <option value="Alumni" <?php echo ($membership_type == 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                    <option value="Faculty" <?php echo ($membership_type == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                </select>
            </div>
            
            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>