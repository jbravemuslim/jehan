<?php
$page_title = "Auth Bypass Challenge (Secure)";
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('../../login.php');
}

$message = '';

// SECURE: Strict comparison
if (isset($_POST['username']) && isset($_POST['password'])) {
    $input_user = $_POST['username'];
    $input_pass = $_POST['password'];
    
    // SECURE: Strict comparison (===) and proper password
    $correct_pass = 'super_secure_password_123';
    
    if ($input_user === 'admin' && $input_pass === $correct_pass) {
        $message = '<span class="text-green-400">✅ Access Granted!</span>';
    } else {
        $message = '<span class="text-red-400">❌ Access Denied!</span>';
    }
}
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-green-400 mb-2">
            🔓 Auth Bypass Challenge (Secure Version)
        </h1>
    </div>

    <div class="bg-green-900/30 border border-green-500/50 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <span class="text-2xl mr-3">✅</span>
            <div>
                <h4 class="text-green-400 font-bold text-sm mb-1">Secure Version</h4>
                <p class="text-gray-400 text-xs mb-2">Proteksi yang diterapkan:</p>
                <ul class="text-xs text-gray-400 space-y-1">
                    <li>• Strict comparison (===) mencegah type juggling</li>
                    <li>• Proper password validation</li>
                    <li>• No loose comparison vulnerability</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-ctf-dark border border-green-500/20 rounded-lg p-6 mb-6">
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <form method="POST" class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        placeholder="admin"
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        placeholder="Password required"
                    >
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded transition">
                    Login
                </button>
            </form>

            <?php if ($message): ?>
            <div class="mt-6 bg-black/50 border border-gray-700 rounded px-4 py-3">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-4">
        <h4 class="text-white font-bold text-sm mb-3">📝 Perbedaan Kode:</h4>
        
        <div class="mb-4">
            <p class="text-red-400 text-xs font-bold mb-1">❌ Vulnerable:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                if ($user == 'admin' && $pass == 0) // Loose comparison
            </code>
        </div>
        
        <div>
            <p class="text-green-400 text-xs font-bold mb-1">✅ Secure:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                if ($user === 'admin' && $pass === $correct_pass) // Strict comparison
            </code>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>