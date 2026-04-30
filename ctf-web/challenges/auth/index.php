<?php
$page_title = "Auth Bypass Challenge";
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('../../login.php');
}

$challenge_id = intval($_GET['id']);
$conn = getDBConnection();
$challenge = $conn->query("SELECT * FROM challenges WHERE id = $challenge_id")->fetch_assoc();

if (!$challenge) {
    redirect('../../dashboard.php');
}

$user_id = $_SESSION['user_id'];
$solved = $conn->query("SELECT * FROM submissions WHERE user_id = $user_id AND challenge_id = $challenge_id AND is_correct = 1")->num_rows > 0;

$message = '';

// VULNERABLE: Weak authentication check
if (isset($_POST['username']) && isset($_POST['password'])) {
    $input_user = $_POST['username'];
    $input_pass = $_POST['password'];
    
    // VULNERABLE: Loose comparison (==)
    if ($input_user == 'admin' && $input_pass == 0) {
        $message = '<span class="text-green-400">✅ Access Granted!</span><br>Flag: <span class="text-ctf-accent font-bold">' . $challenge['flag'] . '</span>';
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
        <h1 class="text-3xl font-bold text-white mb-2">
            🔓 <?php echo htmlspecialchars($challenge['title']); ?>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-green-400">● Easy</span>
            <span class="text-gray-500">AUTH BYPASS</span>
            <span class="text-ctf-accent font-bold">🏆 <?php echo $challenge['points']; ?> points</span>
            <?php if ($solved): ?>
            <span class="bg-green-900/50 text-green-400 px-3 py-1 rounded">✓ Solved</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-3">📋 Description</h3>
        <p class="text-gray-400 mb-4">
            <?php echo htmlspecialchars($challenge['description']); ?>
        </p>
        <p class="text-gray-400 mb-4">
            Login system ini menggunakan loose comparison yang bisa di-bypass.
            Cari cara untuk login tanpa password yang benar!
        </p>
        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded p-3 text-sm">
            <span class="text-yellow-400 font-bold">💡 Hint:</span>
            <span class="text-gray-400"> PHP loose comparison (<code class="text-ctf-accent">==</code>) vs strict comparison (<code class="text-ctf-accent">===</code>). Type juggling!</span>
        </div>
    </div>

    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Challenge Area</h3>
        
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Secure Admin Panel</h4>
            
            <form method="POST" class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="admin"
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input 
                        type="text" 
                        name="password" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="Try type juggling..."
                    >
                </div>
                
                <button type="submit" class="w-full bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker font-bold py-3 rounded transition">
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

    <?php if (!$solved): ?>
    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6">
        <h3 class="text-white font-bold mb-4">🚩 Submit Flag</h3>
        
        <form id="flag-form" class="flex gap-3">
            <input type="hidden" name="challenge_id" value="<?php echo $challenge_id; ?>">
            <input 
                type="text" 
                name="flag" 
                id="flag-input"
                class="flex-1 px-4 py-2 bg-ctf-darker border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                placeholder="FLAG{...}"
                required
            >
            <button type="submit" class="bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-6 py-2 rounded font-bold transition">
                Submit
            </button>
        </form>
        
        <div id="flag-result" class="mt-4 hidden"></div>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('flag-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('flag-result');
    
    try {
        const response = await fetch('../../submit_flag.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        resultDiv.classList.remove('hidden');
        
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded">
                    ✅ ${data.message}
                </div>
            `;
            setTimeout(() => window.location.reload(), 2000);
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded">
                    ❌ ${data.message}
                </div>
            `;
        }
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded">
                ❌ Error submitting flag
            </div>
        `;
    }
});
</script>

<?php require_once '../../includes/footer.php'; ?>