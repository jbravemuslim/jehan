<?php
// File: includes/header.php
// Reusable header untuk semua halaman

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'CTF Platform'; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ctf-dark': '#0a0e27',
                        'ctf-darker': '#06081a',
                        'ctf-accent': '#00ff88',
                        'ctf-accent-dark': '#00cc6a',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #06081a 100%);
            min-height: 100vh;
        }
        
        .glow {
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
        }
    </style>
</head>
<body class="text-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-ctf-darker border-b border-ctf-accent/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/ctf-web/" class="text-2xl font-bold text-ctf-accent glow">
                        🚩 CTF Platform
                    </a>
                </div>
                
                <!-- Menu -->
                <div class="flex items-center space-x-4">
                    <a href="/ctf-web/" class="text-gray-300 hover:text-ctf-accent transition">
                        Home
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Menu untuk user yang sudah login -->
                        <a href="/ctf-web/dashboard.php" class="text-gray-300 hover:text-ctf-accent transition">
                            Dashboard
                        </a>
                        
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="/ctf-web/admin/" class="text-yellow-400 hover:text-yellow-300 transition">
                            Admin
                        </a>
                        <?php endif; ?>
                        
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-400">
                                👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                            <span class="text-sm bg-ctf-accent/20 text-ctf-accent px-3 py-1 rounded">
                                🏆 <?php echo $_SESSION['score'] ?? 0; ?> pts
                            </span>
                            <a href="/ctf-web/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Menu untuk guest -->
                        <a href="/ctf-web/login.php" class="text-gray-300 hover:text-ctf-accent transition">
                            Login
                        </a>
                        <a href="/ctf-web/register.php" class="bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-4 py-2 rounded font-semibold transition">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <main class="min-h-screen">