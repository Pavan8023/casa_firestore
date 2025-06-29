<?php
session_start();

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
?>

<!DOCTYPE html>
<html lang="en" class="dark">

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
        
        .hidden-section {
            display: none;
        }
        
        .form-input {
            background: rgba(13, 17, 23, 0.6);
            border: 1px solid rgba(0, 191, 255, 0.3);
            color: #E0E0E0;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .view-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 191, 255, 0.2);
        }
        
        .close-view-btn {
            background: transparent;
            border: none;
            color: #00BFFF;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .tab-button {
            padding: 10px 20px;
            background: rgba(13, 17, 23, 0.5);
            border: none;
            border-bottom: 2px solid transparent;
            color: #E0E0E0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tab-button.active {
            border-bottom: 2px solid #00BFFF;
            color: #00BFFF;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
            background: rgba(13, 17, 23, 0.3);
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .theme-option {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            background: rgba(34, 40, 49, 0.7);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid transparent;
        }
        
        .theme-option:hover, .theme-option.selected {
            border: 1px solid #00BFFF;
            background: rgba(0, 191, 255, 0.1);
        }
        
        .theme-preview {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            margin-right: 15px;
        }
        
        .theme-blue { background: linear-gradient(135deg, #0D1117 50%, #00BFFF 50%); }
        .theme-green { background: linear-gradient(135deg, #0D1117 50%, #2ecc71 50%); }
        .theme-purple { background: linear-gradient(135deg, #0D1117 50%, #9b59b6 50%); }
        .theme-red { background: linear-gradient(135deg, #0D1117 50%, #e74c3c 50%); }
        
        .notification-item {
            padding: 15px;
            background: rgba(34, 40, 49, 0.7);
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            background: rgba(0, 191, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
            color: #00BFFF;
        }
        
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .project-card {
            background: rgba(34, 40, 49, 0.7);
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
            border-left: 4px solid #00BFFF;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
        
        .status-in-progress { background: rgba(254, 228, 64, 0.2); color: #FEE440; }
        .status-completed { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .status-planned { background: rgba(52, 152, 219, 0.2); color: #3498db; }
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
                            <a href="#" class="dropdown-item" onclick="showView('profileView')">
                                <i class="fas fa-user mr-3"></i> My Profile
                            </a>
                            <a href="#" class="dropdown-item" onclick="showView('projectsView')">
                                <i class="fas fa-project-diagram mr-3"></i> My Projects
                            </a>
                            <a href="#" class="dropdown-item" onclick="showView('researchView')">
                                <i class="fas fa-file-alt mr-3"></i> Research Papers
                            </a>
                            <a href="#" class="dropdown-item" onclick="showView('settingsView')">
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
            <!-- Dashboard View (Default) -->
            <div id="dashboardView">
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
                                <button id="addProjectBtn" class="w-full mb-3 py-2 bg-accent text-primary font-bold rounded-lg hover:bg-[#0099CC] transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add Project
                                </button>
                                <button id="addResearchBtn" class="w-full py-2 bg-secondary text-accent border border-accent font-bold rounded-lg hover:bg-primary/20 transition-colors">
                                    <i class="fas fa-file-alt mr-2"></i>Add Research
                                </button>
                            </div>
                        </div>
                        
                        <!-- My Projects Section -->
                        <div class="dashboard-card mt-8">
                            <h3 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fas fa-project-diagram mr-2"></i> My Projects
                            </h3>
                            <div id="projectsList" class="space-y-3">
                                <!-- Projects will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="lg:col-span-2">
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
                            
                            <!-- Add Project Form (Initially Hidden) -->
                            <div id="projectForm" class="hidden mb-8">
                                <h2 class="text-2xl font-bold mb-4 text-highlight">
                                    <i class="fas fa-plus-circle mr-2"></i>Add New Project
                                </h2>
                                <form id="addProjectForm">
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
                                        <button type="button" id="cancelProjectBtn" class="btn-secondary px-4 py-2">Cancel</button>
                                        <button type="submit" class="btn-primary px-4 py-2">Add Project</button>
                                    </div>
                                </form>
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
                        
                        <!-- Research Papers Section -->
                        <div class="dashboard-card mt-8">
                            <h2 class="text-2xl font-bold mb-4">
                                <i class="fas fa-file-alt mr-2"></i>Research Papers
                            </h2>
                            <div id="researchList" class="space-y-4">
                                <!-- Research papers will be loaded here via AJAX -->
                            </div>
                            
                            <button id="addResearchBtn2" class="mt-6 w-full py-3 bg-secondary text-accent border-2 border-dashed border-accent rounded-lg font-bold hover:bg-primary/20 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Research Paper
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile View (Hidden by default) -->
            <div id="profileView" class="hidden-section">
                <div class="dashboard-card">
                    <div class="view-header">
                        <h2 class="text-2xl font-bold text-accent">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </h2>
                        <button class="close-view-btn" onclick="showView('dashboardView')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Profile Info -->
                        <div class="md:col-span-1">
                            <div class="text-center">
                                <div class="relative inline-block">
                                    <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="w-40 h-40 rounded-full mx-auto border-4 border-accent">
                                    <?php if ($loginMethod === 'google'): ?>
                                    <div class="login-badge" style="width: 36px; height: 36px; font-size: 16px; bottom: -15px; right: -15px;" title="Signed in with Google">
                                        <i class="fab fa-google"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <h2 class="text-2xl font-bold mt-6"><?= htmlspecialchars($userName) ?></h2>
                                <p class="text-text/80 mt-2"><?= htmlspecialchars($userEmail) ?></p>
                                
                                <div class="mt-6">
                                    <button class="btn-secondary w-full py-2">
                                        <i class="fas fa-sync mr-2"></i>Update Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Details -->
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-bold mb-4">Personal Information</h3>
                                    <div class="mb-4">
                                        <label class="block mb-2">Full Name</label>
                                        <input type="text" class="form-input" value="<?= htmlspecialchars($userName) ?>">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2">Email Address</label>
                                        <input type="email" class="form-input" value="<?= htmlspecialchars($userEmail) ?>">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2">Membership Type</label>
                                        <select class="form-input">
                                            <option value="student" <?= $membershipType === 'student' ? 'selected' : '' ?>>Student Member</option>
                                            <option value="alumni" <?= $membershipType === 'alumni' ? 'selected' : '' ?>>Alumni Member</option>
                                            <option value="faculty" <?= $membershipType === 'faculty' ? 'selected' : '' ?>>Faculty Member</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-bold mb-4">Account Security</h3>
                                    <div class="mb-4">
                                        <label class="block mb-2">Current Password</label>
                                        <input type="password" class="form-input">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2">New Password</label>
                                        <input type="password" class="form-input">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block mb-2">Confirm New Password</label>
                                        <input type="password" class="form-input">
                                    </div>
                                    
                                    <button class="btn-primary w-full py-2">
                                        <i class="fas fa-lock mr-2"></i>Update Password
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <h3 class="text-lg font-bold mb-4">Social Profiles</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">
                                            <i class="fab fa-linkedin-in text-white"></i>
                                        </div>
                                        <input type="text" class="form-input" placeholder="LinkedIn Profile">
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center mr-3">
                                            <i class="fab fa-github text-white"></i>
                                        </div>
                                        <input type="text" class="form-input" placeholder="GitHub Profile">
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-400 flex items-center justify-center mr-3">
                                            <i class="fab fa-twitter text-white"></i>
                                        </div>
                                        <input type="text" class="form-input" placeholder="Twitter Profile">
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center mr-3">
                                            <i class="fab fa-discord text-white"></i>
                                        </div>
                                        <input type="text" class="form-input" placeholder="Discord Username">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Projects View (Hidden by default) -->
            <div id="projectsView" class="hidden-section">
                <div class="dashboard-card">
                    <div class="view-header">
                        <h2 class="text-2xl font-bold text-accent">
                            <i class="fas fa-project-diagram mr-2"></i>My Projects
                        </h2>
                        <button class="close-view-btn" onclick="showView('dashboardView')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex space-x-2">
                            <button class="tab-button active" data-tab="all-projects">All Projects</button>
                            <button class="tab-button" data-tab="active-projects">Active</button>
                            <button class="tab-button" data-tab="completed-projects">Completed</button>
                        </div>
                        
                        <button id="addProjectBtn2" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add Project
                        </button>
                    </div>
                    
                    <div id="projectsFullList" class="project-grid">
                        <!-- Projects will be loaded here via AJAX -->
                    </div>
                </div>
            </div>
            
            <!-- Research Papers View (Hidden by default) -->
            <div id="researchView" class="hidden-section">
                <div class="dashboard-card">
                    <div class="view-header">
                        <h2 class="text-2xl font-bold text-accent">
                            <i class="fas fa-file-alt mr-2"></i>Research Papers
                        </h2>
                        <button class="close-view-btn" onclick="showView('dashboardView')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex space-x-2">
                            <button class="tab-button active" data-tab="all-research">All Research</button>
                            <button class="tab-button" data-tab="published-research">Published</button>
                            <button class="tab-button" data-tab="draft-research">Drafts</button>
                        </div>
                        
                        <button id="addResearchBtn3" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add Research
                        </button>
                    </div>
                    
                    <div id="researchFullList">
                        <!-- Research papers will be loaded here via AJAX -->
                    </div>
                </div>
            </div>
            
            <!-- Settings View (Hidden by default) -->
            <div id="settingsView" class="hidden-section">
                <div class="dashboard-card">
                    <div class="view-header">
                        <h2 class="text-2xl font-bold text-accent">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </h2>
                        <button class="close-view-btn" onclick="showView('dashboardView')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Navigation -->
                        <div class="md:col-span-1">
                            <div class="space-y-2">
                                <button class="tab-button active w-full text-left" data-tab="appearance">
                                    <i class="fas fa-palette mr-2"></i>Appearance
                                </button>
                                <button class="tab-button w-full text-left" data-tab="notifications">
                                    <i class="fas fa-bell mr-2"></i>Notifications
                                </button>
                                <button class="tab-button w-full text-left" data-tab="privacy">
                                    <i class="fas fa-shield-alt mr-2"></i>Privacy
                                </button>
                                <button class="tab-button w-full text-left" data-tab="account">
                                    <i class="fas fa-user-cog mr-2"></i>Account
                                </button>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="md:col-span-3">
                            <!-- Appearance Settings -->
                            <div id="appearance" class="tab-content active">
                                <h3 class="text-xl font-bold mb-4">Theme Settings</h3>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Color Theme</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="theme-option selected">
                                            <div class="theme-preview theme-blue"></div>
                                            <span>Blue Theme</span>
                                        </div>
                                        <div class="theme-option">
                                            <div class="theme-preview theme-green"></div>
                                            <span>Green Theme</span>
                                        </div>
                                        <div class="theme-option">
                                            <div class="theme-preview theme-purple"></div>
                                            <span>Purple Theme</span>
                                        </div>
                                        <div class="theme-option">
                                            <div class="theme-preview theme-red"></div>
                                            <span>Red Theme</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Dark Mode</h4>
                                    <div class="flex items-center">
                                        <label class="switch mr-3">
                                            <input type="checkbox" checked>
                                            <span class="slider round"></span>
                                        </label>
                                        <span>Enable Dark Mode</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold mb-3">Layout</h4>
                                    <select class="form-input">
                                        <option>Default Layout</option>
                                        <option>Compact Layout</option>
                                        <option>Detailed Layout</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Notification Settings -->
                            <div id="notifications" class="tab-content">
                                <h3 class="text-xl font-bold mb-4">Notification Preferences</h3>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Email Notifications</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span>Project updates</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Research paper feedback</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Event reminders</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>New messages</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold mb-3">Push Notifications</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span>Important announcements</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Deadline reminders</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>New comments</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Privacy Settings -->
                            <div id="privacy" class="tab-content">
                                <h3 class="text-xl font-bold mb-4">Privacy Settings</h3>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Profile Visibility</h4>
                                    <select class="form-input">
                                        <option>Public - Anyone can see my profile</option>
                                        <option>Members Only - Only CASA members can see my profile</option>
                                        <option selected>Private - Only I can see my profile</option>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Activity Sharing</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span>Show my projects on public profile</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Show my research papers</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Show my activity status</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold mb-3">Data Sharing</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span>Allow data for research purposes</span>
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Share analytics with CASA</span>
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Account Settings -->
                            <div id="account" class="tab-content">
                                <h3 class="text-xl font-bold mb-4">Account Settings</h3>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Account Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-2">Username</label>
                                            <input type="text" class="form-input" value="<?= htmlspecialchars($userName) ?>">
                                        </div>
                                        <div>
                                            <label class="block mb-2">Email</label>
                                            <input type="email" class="form-input" value="<?= htmlspecialchars($userEmail) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <h4 class="font-bold mb-3">Danger Zone</h4>
                                    <div class="bg-red-900/20 p-4 rounded-lg border border-red-700">
                                        <div class="mb-4">
                                            <h5 class="font-bold text-red-400 mb-2">Deactivate Account</h5>
                                            <p class="text-sm">Temporarily deactivate your account. Your data will be preserved.</p>
                                            <button class="btn-secondary mt-2 bg-transparent border-red-500 text-red-400 hover:bg-red-900/30">
                                                Deactivate Account
                                            </button>
                                        </div>
                                        
                                        <div>
                                            <h5 class="font-bold text-red-400 mb-2">Delete Account</h5>
                                            <p class="text-sm">Permanently delete your account and all associated data.</p>
                                            <button class="btn-secondary mt-2 bg-transparent border-red-700 text-red-500 hover:bg-red-900/30">
                                                Delete Account
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <button class="btn-primary w-full py-3">
                                        <i class="fas fa-save mr-2"></i>Save All Changes
                                    </button>
                                </div>
                            </div>
                        </div>
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
            
            // Load initial data
            loadProjects();
            loadResearchPapers();
            
            // Add project button functionality
            document.getElementById('addProjectBtn').addEventListener('click', function() {
                document.getElementById('projectForm').classList.remove('hidden');
            });
            
            document.getElementById('addProjectBtn2').addEventListener('click', function() {
                document.getElementById('projectForm').classList.remove('hidden');
                showView('dashboardView');
            });
            
            document.getElementById('addResearchBtn').addEventListener('click', showResearchForm);
            document.getElementById('addResearchBtn2').addEventListener('click', showResearchForm);
            document.getElementById('addResearchBtn3').addEventListener('click', showResearchForm);
            
            document.getElementById('cancelProjectBtn').addEventListener('click', function() {
                document.getElementById('projectForm').classList.add('hidden');
            });
            
            // Project form submission
            document.getElementById('addProjectForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('add-project.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadProjects();
                        document.getElementById('projectForm').classList.add('hidden');
                        this.reset();
                        updateStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            });
            
            // Tab functionality
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons in the same container
                    const container = this.closest('.dashboard-card');
                    container.querySelectorAll('.tab-button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show corresponding tab content
                    const tabId = this.getAttribute('data-tab');
                    const tabContent = container.querySelector(`#${tabId}`);
                    
                    if (tabContent) {
                        container.querySelectorAll('.tab-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        tabContent.classList.add('active');
                    }
                });
            });
            
            // Theme selection
            document.querySelectorAll('.theme-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.theme-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });
        });
        
        function showResearchForm() {
            alert('Research form will open in a modal. Implementation similar to project form.');
            // Similar implementation to project form
        }
        
        function showView(viewId) {
            // Hide all views
            document.querySelectorAll('main > div').forEach(view => {
                view.classList.add('hidden-section');
            });
            
            // Show the requested view
            document.getElementById(viewId).classList.remove('hidden-section');
            
            // Load data if needed
            if (viewId === 'projectsView') {
                loadFullProjects();
            }
            else if (viewId === 'researchView') {
                loadFullResearch();
            }
        }
        
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
        
        function loadFullProjects() {
            $.ajax({
                url: 'get-projects.php?full=1',
                method: 'GET',
                success: function(data) {
                    $('#projectsFullList').html(data);
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
        
        function loadFullResearch() {
            $.ajax({
                url: 'get-research.php?full=1',
                method: 'GET',
                success: function(data) {
                    $('#researchFullList').html(data);
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
    </script>
</body>
</html>