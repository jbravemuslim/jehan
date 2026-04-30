<?php
$page_title = "SQL Injection Challenge";
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

// Check if solved
$user_id = $_SESSION['user_id'];
$solved = $conn->query("SELECT * FROM submissions WHERE user_id = $user_id AND challenge_id = $challenge_id AND is_correct = 1")->num_rows > 0;
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Challenge Header -->
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">
            💉 <?php echo htmlspecialchars($challenge['title']); ?>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-green-400">● Easy</span>
            <span class="text-gray-500">SQL INJECTION</span>
            <span class="text-ctf-accent font-bold">🏆 <?php echo $challenge['points']; ?> points</span>
            <?php if ($solved): ?>
            <span class="bg-green-900/50 text-green-400 px-3 py-1 rounded">✓ Solved</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Challenge Description -->
    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-3">📋 Description</h3>
        <p class="text-gray-400 mb-4">
            <?php echo htmlspecialchars($challenge['description']); ?>
        </p>
        <p class="text-gray-400 mb-4">
            Kami menemukan website lama yang masih menggunakan login system yang vulnerable.
            Bisakah kamu bypass authentication dan login sebagai admin tanpa password?
        </p>
        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded p-3 text-sm">
            <span class="text-yellow-400 font-bold">💡 Hint:</span>
            <span class="text-gray-400"> Coba lihat bagaimana query SQL dibentuk. Apa yang terjadi jika kamu memasukkan karakter khusus seperti <code class="text-ctf-accent">'</code> atau <code class="text-ctf-accent">--</code>?</span>
        </div>
    </div>

    <!-- Challenge Area -->
    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Challenge Area</h3>
        
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Admin Login Panel</h4>
            
            <form id="sqli-form" class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                    <input 
                        type="text" 
                        id="sqli-username" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="admin"
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="sqli-password" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="••••••••"
                    >
                </div>
                
                <button type="submit" class="w-full bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker font-bold py-3 rounded transition">
                    Login to Admin Panel
                </button>
            </form>
            
            <div id="sqli-result" class="mt-4 hidden"></div>
        </div>
    </div>

    <!-- Submit Flag -->
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
// SQLi Form Handler (simulated)
document.getElementById('sqli-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('sqli-username').value;
    const password = document.getElementById('sqli-password').value;
    const resultDiv = document.getElementById('sqli-result');
    
    // Simulate SQL query
    const query = `SELECT * FROM admin WHERE username = '${username}' AND password = '${password}'`;
    
    // Check if bypass successful
    const bypassPatterns = [
        "' OR '1'='1",
        "' OR 1=1--",
        "admin'--",
        "' OR ''='",
        "admin' OR '1'='1"
    ];
    
    const isBypassed = bypassPatterns.some(pattern => username.includes(pattern.split("'")[0]));
    
    resultDiv.classList.remove('hidden');
    
    if (isBypassed) {
        resultDiv.innerHTML = `
            <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded">
                <p class="font-bold mb-2">✅ Login Successful!</p>
                <p class="text-sm mb-2">Welcome, Admin!</p>
                <code class="text-xs bg-black/50 p-2 rounded block mb-3">Query: ${query}</code>
                <p class="text-sm font-bold text-green-400">Flag: <?php echo $challenge['flag']; ?></p>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded">
                <p class="font-bold mb-2">❌ Login Failed</p>
                <p class="text-sm mb-2">Invalid username or password</p>
                <code class="text-xs bg-black/50 p-2 rounded block">Query: ${query}</code>
            </div>
        `;
    }
});

// Flag Submit Handler
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