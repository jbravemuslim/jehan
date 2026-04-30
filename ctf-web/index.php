<?php
// File: index.php
// Landing page CTF Platform

$page_title = "Home - CTF Platform";
require_once 'includes/header.php';
require_once 'config/db.php';

// Get total challenges and users
$conn = getDBConnection();
$total_challenges = $conn->query("SELECT COUNT(*) as total FROM challenges")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$conn->close();
?>

<!-- Hero Section -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6">
            <span class="text-white">Capture The</span>
            <span class="text-ctf-accent glow">Flag</span>
        </h1>
        
        <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">
            Asah skill cybersecurity kamu melalui challenge yang realistic.
            Dari SQL Injection sampai Authentication Bypass.
        </p>
        
        <div class="flex justify-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/ctf-web/dashboard.php" class="bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-8 py-3 rounded-lg font-bold text-lg transition hover-glow">
                    🎯 Go to Dashboard
                </a>
            <?php else: ?>
                <a href="/ctf-web/register.php" class="bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-8 py-3 rounded-lg font-bold text-lg transition hover-glow">
                    🚀 Start Hacking
                </a>
                <a href="/ctf-web/login.php" class="bg-gray-700 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-bold text-lg transition">
                    🔐 Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-ctf-darker/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-ctf-dark/50 p-6 rounded-lg border border-ctf-accent/20">
                <div class="text-4xl font-bold text-ctf-accent mb-2"><?php echo $total_challenges; ?></div>
                <div class="text-gray-400">Challenges</div>
            </div>
            
            <div class="bg-ctf-dark/50 p-6 rounded-lg border border-ctf-accent/20">
                <div class="text-4xl font-bold text-ctf-accent mb-2"><?php echo $total_users; ?></div>
                <div class="text-gray-400">Hackers</div>
            </div>
            
            <div class="bg-ctf-dark/50 p-6 rounded-lg border border-ctf-accent/20">
                <div class="text-4xl font-bold text-ctf-accent mb-2">4</div>
                <div class="text-gray-400">Categories</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">
            <span class="text-ctf-accent">🎯</span> Challenge Categories
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- SQL Injection -->
            <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 hover:border-ctf-accent/50 transition hover-glow">
                <div class="text-4xl mb-4">💉</div>
                <h3 class="text-xl font-bold text-ctf-accent mb-2">SQL Injection</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Eksploitasi database melalui input yang tidak tersanitasi
                </p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-green-400">● Easy</span>
                    <span class="text-gray-500">100 pts</span>
                </div>
            </div>
            
            <!-- XSS -->
            <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 hover:border-ctf-accent/50 transition hover-glow">
                <div class="text-4xl mb-4">🍪</div>
                <h3 class="text-xl font-bold text-ctf-accent mb-2">XSS</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Cross-Site Scripting untuk steal cookies dan session
                </p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-yellow-400">● Medium</span>
                    <span class="text-gray-500">150 pts</span>
                </div>
            </div>
            
            <!-- File Upload -->
            <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 hover:border-ctf-accent/50 transition hover-glow">
                <div class="text-4xl mb-4">📤</div>
                <h3 class="text-xl font-bold text-ctf-accent mb-2">File Upload</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Upload malicious file untuk remote code execution
                </p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-red-400">● Hard</span>
                    <span class="text-gray-500">200 pts</span>
                </div>
            </div>
            
            <!-- Auth Bypass -->
            <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 hover:border-ctf-accent/50 transition hover-glow">
                <div class="text-4xl mb-4">🔓</div>
                <h3 class="text-xl font-bold text-ctf-accent mb-2">Auth Bypass</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Bypass authentication tanpa kredensial yang valid
                </p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-green-400">● Easy</span>
                    <span class="text-gray-500">100 pts</span>
                </div>
            </div>

                        <!-- Di bagian categories, tambahkan card OSINT -->
            <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 hover:border-ctf-accent/50 transition hover-glow">
                <div class="text-4xl mb-4">🔍</div>
                <h3 class="text-xl font-bold text-ctf-accent mb-2">OSINT</h3>
                <p class="text-gray-400 text-sm mb-4">
                    Open Source Intelligence untuk investigasi digital
                </p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-green-400">● Easy - Hard</span>
                    <span class="text-gray-500">100-200 pts</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-20 bg-ctf-darker/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-12">
            <span class="text-ctf-accent">📚</span> How It Works
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-ctf-accent/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">1️⃣</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Register</h3>
                <p class="text-gray-400">
                    Buat akun gratis dan mulai journey kamu
                </p>
            </div>
            
            <div class="text-center">
                <div class="bg-ctf-accent/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">2️⃣</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Hack</h3>
                <p class="text-gray-400">
                    Eksploitasi vulnerability dan temukan flag
                </p>
            </div>
            
            <div class="text-center">
                <div class="bg-ctf-accent/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">3️⃣</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Submit</h3>
                <p class="text-gray-400">
                    Submit flag dan dapatkan poin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Warning Banner -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded-lg p-6">
            <div class="flex items-start">
                <span class="text-3xl mr-4">⚠️</span>
                <div>
                    <h3 class="text-yellow-400 font-bold mb-2">Ethical Hacking Only!</h3>
                    <p class="text-gray-300 text-sm">
                        Platform ini dibuat untuk tujuan pembelajaran. Semua teknik yang dipelajari di sini 
                        hanya boleh digunakan untuk ethical hacking, penetration testing yang legal, 
                        atau security research. Penggunaan ilegal akan ditindak sesuai hukum yang berlaku.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>