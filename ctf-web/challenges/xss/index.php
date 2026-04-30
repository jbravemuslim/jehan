<?php
$page_title = "XSS Challenge";
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

// Vulnerable: Get comment from URL (XSS)
$comment = isset($_GET['comment']) ? $_GET['comment'] : '';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">
            🍪 <?php echo htmlspecialchars($challenge['title']); ?>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-yellow-400">● Medium</span>
            <span class="text-gray-500">XSS</span>
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
            Website ini memiliki fitur comment yang vulnerable terhadap XSS.
            Buat payload yang bisa mencuri cookie admin untuk mendapatkan flag!
        </p>
        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded p-3 text-sm">
            <span class="text-yellow-400 font-bold">💡 Hint:</span>
            <span class="text-gray-400"> Parameter <code class="text-ctf-accent">comment</code> di URL langsung di-render tanpa sanitasi. Coba inject <code class="text-ctf-accent">&lt;script&gt;</code> tag!</span>
        </div>
    </div>

    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Challenge Area</h3>
        
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Comment Section</h4>
            
            <form method="GET" class="max-w-md mx-auto mb-6">
                <input type="hidden" name="id" value="<?php echo $challenge_id; ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Your Comment</label>
                    <textarea 
                        name="comment" 
                        rows="3"
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        placeholder="Enter your comment..."
                    ><?php echo htmlspecialchars($comment); ?></textarea>
                </div>
                <button type="submit" class="w-full bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker font-bold py-3 rounded transition">
                    Post Comment
                </button>
            </form>

            <?php if ($comment): ?>
            <div class="bg-black/50 border border-gray-700 rounded p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">👤</div>
                    <div class="flex-1">
                        <div class="font-bold text-white mb-1"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <!-- VULNERABLE: Direct output without escaping -->
                        <div class="text-gray-300"><?php echo $comment; ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-6 bg-blue-900/20 border border-blue-500/50 rounded p-3 text-sm">
                <span class="text-blue-400 font-bold">ℹ️ Info:</span>
                <span class="text-gray-400"> Admin cookie: <code class="text-ctf-accent">admin_session=<?php echo $challenge['flag']; ?></code></span>
            </div>
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