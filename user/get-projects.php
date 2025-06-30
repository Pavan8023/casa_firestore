<?php
session_start();
require 'db.php';

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
        <div class="project-item bg-primary/30 p-4 rounded-lg border-l-4 border-accent mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-lg">{$project['title']}</h3>
                    <p class="text-sm text-text/80 mt-1">Type: {$project['type']}</p>
                    <p class="text-sm text-text/80 mt-2">{$project['description']}</p>
                </div>
                <div class="flex space-x-2">
                    <button class="edit-project text-accent hover:text-highlight" data-id="{$project['id']}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="delete-project text-red-500 hover:text-red-300" data-id="{$project['id']}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span class="px-2 py-1 rounded-full text-xs $statusColor">
                    {$project['status']}
                </span>
                <span class="text-xs text-text/70">
                    Created: {$project['created_at']}
                </span>
            </div>
        </div>
HTML;
    }
} else {
    echo '<div class="text-center py-8 text-text/60">No projects found. Add your first project!</div>';
}
?>