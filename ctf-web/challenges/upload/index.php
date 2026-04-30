<?php
$page_title = "File Upload Challenge";
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
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // VULNERABLE: No file type validation
    $filename = basename($file['name']);
    $target = __DIR__ . '/uploads/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        $uploaded_file = 'uploads/' . $filename;
        $message = 'File uploaded successfully!';
        
        // Check if PHP file uploaded (trigger flag reveal)
        if (pathinfo($filename, PATHINFO_EXTENSION) == 'php') {
            $message .= '<br><span class="text-ctf-accent font-bold">Flag: ' . $challenge['flag'] . '</span>';
        }
    } else {
        $message = 'Upload failed!';
    }
}
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">
            📤 <?php echo htmlspecialchars($challenge['title']); ?>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-red-400">● Hard</span>
            <span class="text-gray-500">FILE UPLOAD</span>
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
            Website ini memiliki fitur upload file tanpa validasi yang proper.
            Upload PHP shell untuk mendapatkan remote code execution!
        </p>
        <div class="bg-yellow-900/20 border border-yellow-600/50 rounded p-3 text-sm">
            <span class="text-yellow-400 font-bold">💡 Hint:</span>
            <span class="text-gray-400"> Tidak ada validasi file extension. Coba upload file <code class="text-ctf-accent">.php</code> untuk execute arbitrary code!</span>
        </div>
    </div>

    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Challenge Area</h3>
        
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">File Upload Portal</h4>
            
            <form method="POST" enctype="multipart/form-data" class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select File</label>
                    <input 
                        type="file" 
                        name="file" 
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-ctf-accent text-white"
                        required
                    >
                </div>
                
                <button type="submit" class="w-full bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker font-bold py-3 rounded transition">
                    Upload File
                </button>
            </form>

            <?php if ($message): ?>
            <div class="mt-6 bg-<?php echo strpos($message, 'success') !== false ? 'green' : 'red'; ?>-900/50 border border-<?php echo strpos($message, 'success') !== false ? 'green' : 'red'; ?>-500 text-<?php echo strpos($message, 'success') !== false ? 'green' : 'red'; ?>-200 px-4 py-3 rounded">
                <?php echo $message; ?>
                <?php if ($uploaded_file): ?>
                <br><a href="<?php echo $uploaded_file; ?>" class="text-ctf-accent hover:underline text-sm" target="_blank">View uploaded file</a>
                <?php endif; ?>
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