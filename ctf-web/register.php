<?php
// File: register.php
// Registration page (SECURE VERSION)

$page_title = "Register - CTF Platform";
require_once 'config/db.php';
require_once 'includes/header.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi tidak cocok!';
    } else {
        $conn = getDBConnection();
        
        // Check jika username atau email sudah ada (SECURE: gunakan prepared statement)
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Username atau email sudah terdaftar!';
        } else {
            // Hash password dengan bcrypt (SECURE)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user baru (SECURE: gunakan prepared statement)
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
                // Auto redirect ke login setelah 2 detik
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registrasi gagal! Coba lagi.';
            }
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
            <h2 class="text-3xl font-bold text-ctf-accent glow">
                🚀 Create Account
            </h2>
            <p class="mt-2 text-gray-400">
                Join ribuan hackers lainnya
            </p>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($error): ?>
        <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded mb-4">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-4">
            ✅ <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <!-- Registration Form -->
        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-8">
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
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="hackerman"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        required
                    >
                </div>
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="hackerman@ctf.local"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required
                    >
                </div>
                
                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="••••••••"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm Password
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="w-full px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker font-bold py-3 rounded transition hover-glow"
                >
                    🚀 Register
                </button>
            </form>
            
            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-gray-400 text-sm">
                    Sudah punya akun? 
                    <a href="login.php" class="text-ctf-accent hover:underline">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Security Info -->
        <div class="mt-6 bg-blue-900/20 border border-blue-500/50 rounded-lg p-4">
            <div class="flex items-start">
                <span class="text-2xl mr-3">🔒</span>
                <div>
                    <h4 class="text-blue-400 font-bold text-sm mb-1">Secure Registration</h4>
                    <p class="text-gray-400 text-xs">
                        Password di-hash dengan bcrypt • Prepared statements mencegah SQL Injection
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>