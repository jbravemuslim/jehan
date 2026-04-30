<?php
// File: login_secure.php
// Login page yang SECURE (patched dari SQL Injection)
// ✅ SECURE VERSION - Gunakan ini di production!

$page_title = "Login (Secure) - CTF Platform";
require_once 'config/db.php';
require_once 'includes/header.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $conn = getDBConnection();
        
        // ✅ SECURE: Gunakan prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ✅ SECURE: Verify password dengan password_verify (bcrypt)
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['score'] = $user['score'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // Redirect ke dashboard
                redirect('dashboard.php');
            } else {
                $error = 'Username atau password salah!';
            }
        } else {
            $error = 'Username atau password salah!';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-green-400 glow">
                🔐 Login (Secure Version)
            </h2>
            <p class="mt-2 text-gray-400">
                Versi aman dari SQL Injection
            </p>
        </div>
        
        <!-- Security Info -->
        <div class="mb-6 bg-green-900/30 border border-green-500/50 rounded-lg p-4">
            <div class="flex items-start">
                <span class="text-2xl mr-3">✅</span>
                <div>
                    <h4 class="text-green-400 font-bold text-sm mb-1">Secure Version</h4>
                    <p class="text-gray-400 text-xs mb-2">
                        Halaman ini sudah di-patch:
                    </p>
                    <ul class="text-xs text-gray-400 space-y-1">
                        <li>• Prepared statements mencegah SQL Injection</li>
                        <li>• Password di-verify dengan bcrypt</li>
                        <li>• Input di-sanitasi dengan trim()</li>
                    </ul>
                    <a href="login.php" class="text-yellow-400 text-xs hover:underline mt-2 inline-block">
                        👉 Lihat vulnerable version
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($error): ?>
        <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-4">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <div class="bg-ctf-dark border border-green-500/20 rounded-lg p-8">
            <form method="POST" action="">
                <!-- Username -->
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        placeholder="hackerman"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        required
                    >
                </div>
                
                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded transition"
                >
                    🔐 Login Securely
                </button>
            </form>
            
            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="text-gray-400 text-sm">
                    Belum punya akun? 
                    <a href="register.php" class="text-green-400 hover:underline">
                        Register di sini
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Code Comparison -->
        <div class="mt-6 bg-ctf-darker border border-gray-700 rounded-lg p-4">
            <h4 class="text-white font-bold text-sm mb-3">📝 Perbedaan Kode:</h4>
            
            <div class="mb-4">
                <p class="text-red-400 text-xs font-bold mb-1">❌ Vulnerable:</p>
                <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
                </code>
            </div>
            
            <div>
                <p class="text-green-400 text-xs font-bold mb-1">✅ Secure:</p>
                <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");<br>
                    $stmt->bind_param("s", $username);
                </code>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>