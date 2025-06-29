<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Not authenticated');
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();

if (count($projects) > 0) {
    foreach ($projects as $project) {
        $statusColor = $project['status'] === 'completed' ? 'bg-green-500' : 
                      ($project['status'] === 'in_progress' ? 'bg-yellow-500' : 'bg-blue-500');
                      
        echo <<<HTML
        <div class="project-item bg-primary/30 p-3 rounded-lg flex items-center justify-between mb-2 hover:bg-primary/40 transition-colors">
            <div>
                <h4 class="font-bold">{$project['title']}</h4>
                <p class="text-sm text-text/80 truncate max-w-xs">{$project['description']}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 rounded-full text-xs $statusColor">
                    {$project['status']}
                </span>
                <button class="text-accent hover:text-highlight">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
HTML;
    }
} else {
    echo '<p class="text-center py-4 text-text/60">No projects yet. Add your first project!</p>';
}
?>