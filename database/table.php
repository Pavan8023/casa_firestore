<?php
// Database connection parameters
$servername = "localhost"; // Change if not running locally
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "casa_members";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create users table
$sql = "CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Run the query
if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created successfully in database 'casa_members'.";
} else {
    echo "Error creating table: " . $conn->error;
}

// Close connection
$conn->close();
?>
