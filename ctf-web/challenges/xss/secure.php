<?php
$page_title = "XSS Challenge (Secure)";
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

// SECURE: Sanitize input
$comment = isset($_GET['comment']) ? htmlspecialchars($_GET['comment'], ENT_QUOTES, 'UTF-8') : '';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-green-400 mb-2">
            🍪 XSS Challenge (Secure Version)
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-yellow-400">● Medium</span>
            <span class="text-gray-500">XSS - PATCHED</span>
        </div>
    </div>

    <div class="bg-green-900/30 border border-green-500/50 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <span class="text-2xl mr-3">✅</span>
            <div>
                <h4 class="text-green-400 font-bold text-sm mb-1">Secure Version</h4>
                <p class="text-gray-400 text-xs mb-2">Halaman ini sudah di-patch:</p>
                <ul class="text-xs text-gray-400 space-y-1">
                    <li>• Input di-sanitasi dengan htmlspecialchars()</li>
                    <li>• ENT_QUOTES flag untuk escape single/double quotes</li>
                    <li>• UTF-8 encoding untuk prevent encoding attacks</li>
                </ul>
                <a href="index.php?id=<?php echo $challenge_id; ?>" class="text-yellow-400 text-xs hover:underline mt-2 inline-block">
                    👉 Lihat vulnerable version
                </a>
            </div>
        </div>
    </div>

    <div class="bg-ctf-dark border border-green-500/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Secure Comment Section</h3>
        
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <form method="GET" class="max-w-md mx-auto mb-6">
                <input type="hidden" name="id" value="<?php echo $challenge_id; ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Your Comment</label>
                    <textarea 
                        name="comment" 
                        rows="3"
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        placeholder="Enter your comment..."
                    ><?php echo $comment; ?></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded transition">
                    Post Comment
                </button>
            </form>

            <?php if ($comment): ?>
            <div class="bg-black/50 border border-gray-700 rounded p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">👤</div>
                    <div class="flex-1">
                        <div class="font-bold text-white mb-1"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <!-- SECURE: Properly escaped output -->
                        <div class="text-gray-300"><?php echo $comment; ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-4">
        <h4 class="text-white font-bold text-sm mb-3">📝 Perbedaan Kode:</h4>
        
        <div class="mb-4">
            <p class="text-red-400 text-xs font-bold mb-1">❌ Vulnerable:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                $comment = $_GET['comment'];<br>
                echo $comment; // Direct output
            </code>
        </div>
        
        <div>
            <p class="text-green-400 text-xs font-bold mb-1">✅ Secure:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                $comment = htmlspecialchars($_GET['comment'], ENT_QUOTES, 'UTF-8');<br>
                echo $comment; // Safe output
            </code>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>