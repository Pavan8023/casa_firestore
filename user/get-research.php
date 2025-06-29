<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Not authenticated');
}

$db = new Database();
$conn = $db->getConnection();

// Similar implementation to get-projects.php
// Would fetch research papers instead of projects
echo '<p class="text-center py-4 text-text/60">No research papers yet. Add your first research paper!</p>';
?>