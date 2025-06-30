<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../member-login.html');
    exit;
}

// Get user data from session
$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
$profilePic = $_SESSION['profile_pic'] ?? 'https://via.placeholder.com/40';
$membershipType = $_SESSION['membership'] ?? 'student';
$loginMethod = $_SESSION['login_method'] ?? 'email';
$userId = $_SESSION['user_id'] ?? 0;

// Initialize theme settings
$theme = $_SESSION['theme'] ?? 'dark';
$layout = $_SESSION['layout'] ?? 'grid';
?>

<!DOCTYPE html>
<html lang="en" class="<?= $theme ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CASA</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0D1117',
                        accent: '#00BFFF',
                        highlight: '#FEE440',
                        secondary: '#222831',
                        text: '#E0E0E0',
                    },
                    fontFamily: {
                        'headline': ['Poppins', 'Montserrat', 'sans-serif'],
                        'body': ['Inter', 'Nunito', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            background-color: #0D1117;
        }

        .dashboard-card {
            background: rgba(34, 40, 49, 0.9);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 191, 255, 0.3);
        }

        .user-profile {
            transition: all 0.3s;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            min-width: 200px;
            background: rgba(34, 40, 49, 0.95);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 8px;
            z-index: 100;
            backdrop-filter: blur(10px);
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .dropdown-item {
            padding: 12px 16px;
            display: block;
            color: #E0E0E0;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(0, 191, 255, 0.1);
            color: #00BFFF;
        }
        
        .login-badge {
            position: absolute;
            bottom: -8px;
            right: -8px;
            background: #FEE440;
            color: #0D1117;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid #0D1117;
        }
        
        .view-section {
            display: none;
        }
        
        .view-section.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-input {
            background: rgba(13, 17, 23, 0.6);
            border: 1px solid rgba(0, 191, 255, 0.3);
            color: #E0E0E0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-input:focus {
            border-color: #00BFFF;
            box-shadow: 0 0 0 2px rgba(0, 191, 255, 0.3);
            outline: none;
        }
        
        .btn-primary {
            background: #00BFFF;
            color: #0D1117;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #0099CC;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 191, 255, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: #00BFFF;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid #00BFFF;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: rgba(0, 191, 255, 0.1);
        }
        
        .layout-grid .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .layout-list .card-container {
            display: block;
        }
        
        .layout-list .card-item {
            margin-bottom: 15px;
        }
    </style>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="text-text font-body">
    <!-- Particles Background -->
    <div id="particles-js"></div>

    <!-- Content Container -->
    <div class="content-container">
        <!-- Navigation Bar -->
        <nav class="fixed top-0 w-full bg-secondary/80 backdrop-blur-md z-50 shadow-lg">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center">
                            <i class="fas fa-code text-primary text-xl"></i>
                        </div>
                        <a href="../index.html" class="text-2xl font-bold text-accent flex items-center">
                            CASA
                            <span class="ml-1 text-xs bg-highlight text-primary px-2 py-1 rounded-full">AI</span>
                        </a>
                    </div>

                    <!-- User Profile with Dropdown -->
                    <div class="dropdown relative">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="text-right hidden md:block">
                                <p class="font-medium"><?= htmlspecialchars($userName) ?></p>
                                <p class="text-sm text-text/80"><?= htmlspecialchars($userEmail) ?></p>
                            </div>
                            <div class="relative">
                                <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-accent">
                                <?php if ($loginMethod === 'google'): ?>
                                <div class="login-badge" title="Signed in with Google">
                                    G
                                </div>
                                <?php endif; ?>
                            </div>
                            <i class="fas fa-chevron-down text-sm ml-1"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="dropdown-content">
                            <div class="p-4 border-b border-gray-700">
                                <p class="font-medium"><?= htmlspecialchars($userName) ?></p>
                                <p class="text-xs text-text/80 truncate"><?= htmlspecialchars($userEmail) ?></p>
                                <?php if ($loginMethod === 'google'): ?>
                                <span class="text-xs text-accent mt-1 block">
                                    <i class="fab fa-google mr-1"></i> Signed in with Google
                                </span>
                                <?php endif; ?>
                            </div>
                            <a href="#" class="dropdown-item" data-view="profile">
                                <i class="fas fa-user mr-3"></i> My Profile
                            </a>
                            <a href="#" class="dropdown-item" data-view="projects">
                                <i class="fas fa-project-diagram mr-3"></i> My Projects
                            </a>
                            <a href="#" class="dropdown-item" data-view="research">
                                <i class="fas fa-file-alt mr-3"></i> Research Papers
                            </a>
                            <a href="#" class="dropdown-item" data-view="settings">
                                <i class="fas fa-cog mr-3"></i> Settings
                            </a>
                            <a href="logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-3"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-24">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- User Profile Card -->
                <div class="lg:col-span-1">
                    <div class="dashboard-card user-profile">
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="w-32 h-32 rounded-full mx-auto border-4 border-accent">
                                <?php if ($loginMethod === 'google'): ?>
                                <div class="login-badge" style="width: 32px; height: 32px; font-size: 14px; bottom: -12px; right: -12px;" title="Signed in with Google">
                                    <i class="fab fa-google"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <h2 class="text-2xl font-bold mt-4"><?= htmlspecialchars($userName) ?></h2>
                            <p class="text-text/80"><?= htmlspecialchars($userEmail) ?></p>
                            <p class="mt-2 px-3 py-1 bg-primary/50 rounded-full inline-block">
                                <i class="fas fa-user-graduate mr-2"></i>
                                <?= ucfirst($membershipType) ?> Member
                            </p>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="text-lg font-bold mb-3">Quick Actions</h3>
                            <button data-view="projects" class="w-full mb-3 py-2 bg-accent text-primary font-bold rounded-lg hover:bg-[#0099CC] transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Project
                            </button>
                            <button data-view="research" class="w-full py-2 bg-secondary text-accent border border-accent font-bold rounded-lg hover:bg-primary/20 transition-colors">
                                <i class="fas fa-file-alt mr-2"></i>Add Research
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div class="lg:col-span-2">
                    <!-- Dashboard View -->
                    <div id="dashboardView" class="view-section active">
                        <div class="dashboard-card">
                            <h1 class="text-3xl font-bold mb-6 text-accent">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </h1>
                            
                            <!-- Stats -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                <div class="bg-primary/50 p-4 rounded-lg text-center transition-transform hover:scale-[1.03]">
                                    <p class="text-2xl font-bold" id="projectsCount">0</p>
                                    <p class="text-text/80">Active Projects</p>
                                </div>
                                <div class="bg-primary/50 p-4 rounded-lg text-center transition-transform hover:scale-[1.03]">
                                    <p class="text-2xl font-bold" id="researchCount">0</p>
                                    <p class="text-text/80">Research Papers</p>
                                </div>
                                <div class="bg-primary/50 p-4 rounded-lg text-center transition-transform hover:scale-[1.03]">
                                    <p class="text-2xl font-bold" id="tasksCount">0</p>
                                    <p class="text-text/80">Pending Tasks</p>
                                </div>
                            </div>
                            
                            <!-- Recent Activity -->
                            <h2 class="text-2xl font-bold mb-4">
                                <i class="fas fa-history mr-2"></i>Recent Activity
                            </h2>
                            <div class="space-y-4">
                                <div class="bg-primary/30 p-4 rounded-lg border-l-4 border-accent">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-bold">Joined CASA Portal</h3>
                                        <span class="text-sm text-text/70">Today</span>
                                    </div>
                                    <p class="mt-2 text-sm text-text/80">Welcome to the CASA member portal!</p>
                                </div>
                                
                                <div class="bg-primary/30 p-4 rounded-lg border-l-4 border-highlight">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-bold">Profile Completed</h3>
                                        <span class="text-sm text-text/70">Today</span>
                                    </div>
                                    <p class="mt-2 text-sm text-text/80">Your profile information has been saved</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile View -->
                    <div id="profileView" class="view-section dashboard-card">
                        <h1 class="text-3xl font-bold mb-6 text-accent">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </h1>
                        
                        <form id="profileForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block mb-2">Full Name</label>
                                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($userName) ?>" required>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block mb-2">Email Address</label>
                                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($userEmail) ?>" readonly>
                            </div>
                            
                            <div>
                                <label class="block mb-2">Membership Type</label>
                                <select name="membership" class="form-input">
                                    <option value="student" <?= $membershipType === 'student' ? 'selected' : '' ?>>Student Member</option>
                                    <option value="alumni" <?= $membershipType === 'alumni' ? 'selected' : '' ?>>Alumni Member</option>
                                    <option value="faculty" <?= $membershipType === 'faculty' ? 'selected' : '' ?>>Faculty Member</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block mb-2">Login Method</label>
                                <input type="text" class="form-input" value="<?= ucfirst($loginMethod) ?>" readonly>
                            </div>
                            
                            <div class="md:col-span-2">
                                <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Change Password</h3>
                            </div>
                            
                            <div>
                                <label class="block mb-2">Current Password</label>
                                <input type="password" name="current_password" class="form-input">
                            </div>
                            
                            <div>
                                <label class="block mb-2">New Password</label>
                                <input type="password" name="new_password" class="form-input">
                            </div>
                            
                            <div class="md:col-span-2 flex justify-end space-x-3 mt-4">
                                <button type="button" class="btn-secondary" data-view="dashboard">Cancel</button>
                                <button type="submit" class="btn-primary">Update Profile</button>
                            </div>
                            
                            <div id="profileMessage" class="md:col-span-2 mt-4"></div>
                        </form>
                    </div>
                    
                    <!-- Projects View -->
                    <div id="projectsView" class="view-section dashboard-card">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-3xl font-bold text-accent">
                                <i class="fas fa-project-diagram mr-2"></i>My Projects
                            </h1>
                            <button id="addProjectBtn" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add Project
                            </button>
                        </div>
                        
                        <!-- Projects List -->
                        <div id="projectsList" class="space-y-4">
                            <!-- Projects will be loaded here via AJAX -->
                        </div>
                        
                        <!-- Add/Edit Project Form -->
                        <div id="projectForm" class="mt-8 hidden">
                            <h2 class="text-2xl font-bold mb-4 text-highlight" id="projectFormTitle">
                                Add New Project
                            </h2>
                            <form id="addProjectForm">
                                <input type="hidden" name="project_id" id="projectId">
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Project Title</label>
                                    <input type="text" name="title" class="form-input" required>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block mb-2">Project Type</label>
                                        <select name="type" class="form-input">
                                            <option value="web">Web Application</option>
                                            <option value="mobile">Mobile App</option>
                                            <option value="ai">AI/ML Project</option>
                                            <option value="iot">IoT Project</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-2">Status</label>
                                        <select name="status" class="form-input">
                                            <option value="planned">Planned</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Description</label>
                                    <textarea name="description" rows="3" class="form-input" required></textarea>
                                </div>
                                
                                <div class="flex justify-end space-x-3">
                                    <button type="button" id="cancelProjectBtn" class="btn-secondary">Cancel</button>
                                    <button type="submit" class="btn-primary">Save Project</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Research Papers View -->
                    <div id="researchView" class="view-section dashboard-card">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-3xl font-bold text-accent">
                                <i class="fas fa-file-alt mr-2"></i>Research Papers
                            </h1>
                            <button id="addResearchBtn" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add Research
                            </button>
                        </div>
                        
                        <!-- Research Papers List -->
                        <div id="researchList" class="space-y-4">
                            <!-- Research papers will be loaded here via AJAX -->
                        </div>
                        
                        <!-- Add/Edit Research Form -->
                        <div id="researchForm" class="mt-8 hidden">
                            <h2 class="text-2xl font-bold mb-4 text-highlight" id="researchFormTitle">
                                Add New Research Paper
                            </h2>
                            <form id="addResearchForm">
                                <input type="hidden" name="research_id" id="researchId">
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Title</label>
                                    <input type="text" name="title" class="form-input" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Authors</label>
                                    <input type="text" name="authors" class="form-input" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Abstract</label>
                                    <textarea name="abstract" rows="3" class="form-input" required></textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block mb-2">Published Date</label>
                                        <input type="date" name="published_date" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block mb-2">Conference/Journal</label>
                                        <input type="text" name="conference" class="form-input">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Paper Link</label>
                                    <input type="url" name="paper_link" class="form-input">
                                </div>
                                
                                <div class="flex justify-end space-x-3">
                                    <button type="button" id="cancelResearchBtn" class="btn-secondary">Cancel</button>
                                    <button type="submit" class="btn-primary">Save Research</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Settings View -->
                    <div id="settingsView" class="view-section dashboard-card">
                        <h1 class="text-3xl font-bold mb-6 text-accent">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </h1>
                        
                        <form id="settingsForm">
                            <div class="mb-6">
                                <h2 class="text-xl font-bold mb-4">Theme Settings</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block mb-2">Color Theme</label>
                                        <select name="theme" class="form-input">
                                            <option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                                            <option value="light" <?= $theme === 'light' ? 'selected' : '' ?>>Light Mode</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block mb-2">Layout Preference</label>
                                        <select name="layout" class="form-input">
                                            <option value="grid" <?= $layout === 'grid' ? 'selected' : '' ?>>Grid Layout</option>
                                            <option value="list" <?= $layout === 'list' ? 'selected' : '' ?>>List Layout</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h2 class="text-xl font-bold mb-4">Notification Preferences</h2>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="emailNotifications" name="email_notifications" class="mr-2" checked>
                                        <label for="emailNotifications">Email Notifications</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" id="projectUpdates" name="project_updates" class="mr-2" checked>
                                        <label for="projectUpdates">Project Updates</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" id="researchUpdates" name="research_updates" class="mr-2" checked>
                                        <label for="researchUpdates">Research Updates</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">Save Settings</button>
                            </div>
                            
                            <div id="settingsMessage" class="mt-4"></div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-secondary py-8 mt-20">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-accent font-headline text-xl">CASA</p>
                        <p class="text-sm">© 2024 Computer Applications Student Association</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-accent transition-colors">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="hover:text-accent transition-colors">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="hover:text-accent transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Initialize particles
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: "#00BFFF" },
                    shape: { type: "circle" },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#00BFFF",
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: "none",
                        random: true,
                        straight: false,
                        out_mode: "out",
                        bounce: false
                    }
                },
                interactivity: {
                    detect_on: "window",
                    events: {
                        onhover: { enable: true, mode: "grab" },
                        onclick: { enable: true, mode: "push" },
                        resize: true
                    }
                }
            });
            
            // View switching
            function showView(viewId) {
                $('.view-section').removeClass('active');
                $('#' + viewId + 'View').addClass('active');
                sessionStorage.setItem('currentView', viewId);
                
                // Load data when specific views are opened
                if(viewId === 'projects') {
                    loadProjects();
                }
                else if(viewId === 'research') {
                    loadResearchPapers();
                }
            }
            
            // Restore last view if available
            const currentView = sessionStorage.getItem('currentView') || 'dashboard';
            showView(currentView);
            
            // Menu item click handlers
            $('.dropdown-item[data-view]').on('click', function(e) {
                e.preventDefault();
                const view = $(this).data('view');
                showView(view);
            });
            
            // Quick action buttons
            $('button[data-view]').on('click', function() {
                const view = $(this).data('view');
                showView(view);
            });
            
            // Load projects and research papers
            function loadProjects() {
                $.ajax({
                    url: 'get-projects.php',
                    method: 'GET',
                    success: function(data) {
                        $('#projectsList').html(data);
                        updateStats();
                    }
                });
            }
            
            function loadResearchPapers() {
                $.ajax({
                    url: 'get-research.php',
                    method: 'GET',
                    success: function(data) {
                        $('#researchList').html(data);
                        updateStats();
                    }
                });
            }
            
            function updateStats() {
                const projectCount = $('#projectsList .project-item').length;
                const researchCount = $('#researchList .research-item').length;
                
                $('#projectsCount').text(projectCount);
                $('#researchCount').text(researchCount);
                $('#tasksCount').text(projectCount > 0 ? projectCount * 3 : 0);
            }
            
            // Project form functionality
            $('#addProjectBtn').on('click', function() {
                $('#projectForm').removeClass('hidden');
                $('#projectFormTitle').text('Add New Project');
                $('#projectId').val('');
                $('#addProjectForm')[0].reset();
            });
            
            $('#cancelProjectBtn').on('click', function() {
                $('#projectForm').addClass('hidden');
            });
            
            // Project form submission
            $('#addProjectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'save-project.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status === 'success') {
                            loadProjects();
                            $('#projectForm').addClass('hidden');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            });
            
            // Edit project handler
            $(document).on('click', '.edit-project', function() {
                const projectId = $(this).data('id');
                
                $.ajax({
                    url: 'get-project.php?id=' + projectId,
                    method: 'GET',
                    success: function(project) {
                        $('#projectForm').removeClass('hidden');
                        $('#projectFormTitle').text('Edit Project');
                        $('#projectId').val(project.id);
                        $('input[name="title"]').val(project.title);
                        $('select[name="type"]').val(project.type);
                        $('select[name="status"]').val(project.status);
                        $('textarea[name="description"]').val(project.description);
                    }
                });
            });
            
            // Research form functionality
            $('#addResearchBtn').on('click', function() {
                $('#researchForm').removeClass('hidden');
                $('#researchFormTitle').text('Add New Research Paper');
                $('#researchId').val('');
                $('#addResearchForm')[0].reset();
            });
            
            $('#cancelResearchBtn').on('click', function() {
                $('#researchForm').addClass('hidden');
            });
            
            // Research form submission
            $('#addResearchForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'save-research.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status === 'success') {
                            loadResearchPapers();
                            $('#researchForm').addClass('hidden');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            });
            
            // Edit research handler
            $(document).on('click', '.edit-research', function() {
                const researchId = $(this).data('id');
                
                $.ajax({
                    url: 'get-research.php?id=' + researchId,
                    method: 'GET',
                    success: function(research) {
                        $('#researchForm').removeClass('hidden');
                        $('#researchFormTitle').text('Edit Research Paper');
                        $('#researchId').val(research.id);
                        $('input[name="title"]').val(research.title);
                        $('input[name="authors"]').val(research.authors);
                        $('textarea[name="abstract"]').val(research.abstract);
                        $('input[name="published_date"]').val(research.published_date);
                        $('input[name="conference"]').val(research.conference);
                        $('input[name="paper_link"]').val(research.paper_link);
                    }
                });
            });
            
            // Profile form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'update-profile.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status === 'success') {
                            $('#profileMessage').html(
                                '<div class="success-message p-3 bg-green-900/30 rounded-lg">' +
                                response.message + '</div>'
                            );
                            
                            // Update session data if needed
                            if(response.newName) {
                                $('.user-name').text(response.newName);
                            }
                        } else {
                            $('#profileMessage').html(
                                '<div class="error-message p-3 bg-red-900/30 rounded-lg">' +
                                response.message + '</div>'
                            );
                        }
                    }
                });
            });
            
            // Settings form submission
            $('#settingsForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'save-settings.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status === 'success') {
                            $('#settingsMessage').html(
                                '<div class="success-message p-3 bg-green-900/30 rounded-lg">' +
                                'Settings saved successfully!</div>'
                            );
                            
                            // Update theme immediately
                            if(response.theme) {
                                $('html').removeClass('dark light').addClass(response.theme);
                            }
                            
                            // Update layout
                            if(response.layout) {
                                $('body').removeClass('layout-grid layout-list').addClass('layout-' + response.layout);
                            }
                        } else {
                            $('#settingsMessage').html(
                                '<div class="error-message p-3 bg-red-900/30 rounded-lg">' +
                                response.message + '</div>'
                            );
                        }
                    }
                });
            });
            
            // Apply current layout
            $('body').addClass('layout-<?= $layout ?>');
        });
    </script>
</body>
</html>