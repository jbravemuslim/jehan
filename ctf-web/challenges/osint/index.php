<?php
$page_title = "OSINT Challenge";
require_once '../../config/db.php';
require_once '../../includes/header.php';

if (!isLoggedIn()) {
    redirect('../../login.php');
}

$challenge_id = intval($_GET['id']);
$conn = getDBConnection();
$challenge = $conn->query("SELECT * FROM challenges WHERE id = $challenge_id")->fetch_assoc();

if (!$challenge || $challenge['category'] !== 'osint') {
    redirect('../../dashboard.php');
}

$user_id = $_SESSION['user_id'];
$solved = $conn->query("SELECT * FROM submissions WHERE user_id = $user_id AND challenge_id = $challenge_id AND is_correct = 1")->num_rows > 0;

// Different content based on challenge ID
$challenge_content = '';

if ($challenge_id == 5) { // Hidden in Plain Sight
    $challenge_content = '
    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
        <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Analyze This Image</h4>
        
        <div class="max-w-2xl mx-auto mb-6">
            <img src="assets/challenge1.jpg" alt="Challenge Image" class="w-full rounded border border-gray-700">
        </div>
        
        <div class="bg-blue-900/20 border border-blue-500/50 rounded p-4 text-sm">
            <p class="text-blue-400 font-bold mb-2">💡 Hints:</p>
            <ul class="text-gray-400 space-y-1 text-sm">
                <li>• Foto ini memiliki metadata tersembunyi</li>
                <li>• Coba gunakan tools seperti <code class="text-ctf-accent">exiftool</code> atau online EXIF viewer</li>
                <li>• Perhatikan field "Comment" atau "Description"</li>
                <li>• Download image: <a href="assets/challenge1.jpg" download class="text-ctf-accent hover:underline">challenge1.jpg</a></li>
            </ul>
        </div>
        
        <div class="mt-6 bg-gray-900/50 rounded p-4">
            <h5 class="text-white font-bold mb-3">🛠️ Tools yang bisa digunakan:</h5>
            <ul class="text-gray-400 text-sm space-y-2">
                <li>• Online: <a href="https://exifdata.com" target="_blank" class="text-ctf-accent hover:underline">exifdata.com</a></li>
                <li>• Online: <a href="https://jimpl.com" target="_blank" class="text-ctf-accent hover:underline">jimpl.com</a></li>
                <li>• CLI: <code class="text-ctf-accent">exiftool challenge1.jpg</code></li>
                <li>• Windows: Right-click → Properties → Details</li>
            </ul>
        </div>
    </div>';
    
} elseif ($challenge_id == 6) { // Digital Footprint
    $challenge_content = '
    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
        <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Find the Digital Trail</h4>
        
        <div class="bg-black/50 border border-gray-700 rounded p-6 mb-6">
            <h5 class="text-white font-bold mb-4">Target Information:</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Username:</span>
                    <span class="text-ctf-accent font-mono ml-2">cyb3r_hunt3r_2024</span>
                </div>
                <div>
                    <span class="text-gray-400">Known Platform:</span>
                    <span class="text-white ml-2">GitHub</span>
                </div>
                <div>
                    <span class="text-gray-400">Task:</span>
                    <span class="text-white ml-2">Find their secret repository</span>
                </div>
                <div>
                    <span class="text-gray-400">Flag Location:</span>
                    <span class="text-white ml-2">Hidden in commit message</span>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-900/20 border border-blue-500/50 rounded p-4 text-sm mb-6">
            <p class="text-blue-400 font-bold mb-2">💡 Hints:</p>
            <ul class="text-gray-400 space-y-1 text-sm">
                <li>• Username yang sama sering digunakan di berbagai platform</li>
                <li>• Cek GitHub profile untuk repository publik</li>
                <li>• Flag tersembunyi di commit history salah satu repo</li>
                <li>• Look for repository bernama "secret-notes"</li>
            </ul>
        </div>
        
        <div class="bg-gray-900/50 rounded p-4">
            <h5 class="text-white font-bold mb-3">🔍 OSINT Techniques:</h5>
            <ul class="text-gray-400 text-sm space-y-2">
                <li>• <strong class="text-white">Username Search:</strong> Gunakan <a href="https://namechk.com" target="_blank" class="text-ctf-accent hover:underline">namechk.com</a> atau <a href="https://whatsmyname.app" target="_blank" class="text-ctf-accent hover:underline">whatsmyname.app</a></li>
                <li>• <strong class="text-white">GitHub Search:</strong> <code class="text-ctf-accent">https://github.com/[username]</code></li>
                <li>• <strong class="text-white">Commit History:</strong> Klik repository → Commits → Lihat pesan commit</li>
                <li>• <strong class="text-white">Google Dorking:</strong> <code class="text-ctf-accent">site:github.com "cyb3r_hunt3r_2024"</code></li>
            </ul>
        </div>
        
        <div class="mt-6 bg-yellow-900/20 border border-yellow-600/50 rounded p-4">
            <p class="text-yellow-400 text-sm">
                <strong>Note:</strong> Ini simulasi OSINT challenge. Username adalah fiktif untuk pembelajaran.
                Di challenge real, target adalah akun yang dibuat khusus untuk CTF.
            </p>
        </div>
    </div>';
    
} elseif ($challenge_id == 7) { // Geolocation
    $challenge_content = '
    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
        <h4 class="text-center text-xl font-bold text-ctf-accent mb-6">Find the Location</h4>
        
        <div class="max-w-2xl mx-auto mb-6">
            <img src="assets/challenge3.jpg" alt="Mystery Location" class="w-full rounded border border-gray-700">
        </div>
        
        <div class="bg-black/50 border border-gray-700 rounded p-6 mb-6">
            <h5 class="text-white font-bold mb-4">Task:</h5>
            <p class="text-gray-400 mb-4">
                Foto ini diambil di sebuah landmark terkenal. Tentukan koordinat GPS lokasi ini!
            </p>
            <div class="text-sm">
                <span class="text-gray-400">Format flag:</span>
                <code class="text-ctf-accent ml-2">FLAG{latitude_longitude}</code>
                <p class="text-gray-500 text-xs mt-2">Contoh: FLAG{-6.2088_106.8456} (2 desimal)</p>
            </div>
        </div>
        
        <div class="bg-blue-900/20 border border-blue-500/50 rounded p-4 text-sm mb-6">
            <p class="text-blue-400 font-bold mb-2">💡 Hints:</p>
            <ul class="text-gray-400 space-y-1 text-sm">
                <li>• Perhatikan landmark/bangunan yang terlihat</li>
                <li>• Cari tahu nama bangunan dengan reverse image search</li>
                <li>• Gunakan Google Maps untuk mendapatkan koordinat exact</li>
                <li>• Bulatkan ke 2 desimal: -6.208763 → -6.21</li>
            </ul>
        </div>
        
        <div class="bg-gray-900/50 rounded p-4 mb-6">
            <h5 class="text-white font-bold mb-3">🛠️ Geolocation Tools:</h5>
            <ul class="text-gray-400 text-sm space-y-2">
                <li>• <strong class="text-white">Reverse Image Search:</strong> 
                    <a href="https://images.google.com" target="_blank" class="text-ctf-accent hover:underline">Google Images</a>, 
                    <a href="https://yandex.com/images" target="_blank" class="text-ctf-accent hover:underline">Yandex</a>,
                    <a href="https://tineye.com" target="_blank" class="text-ctf-accent hover:underline">TinEye</a>
                </li>
                <li>• <strong class="text-white">Maps:</strong> Google Maps, Google Earth</li>
                <li>• <strong class="text-white">EXIF Data:</strong> Check GPS coordinates dalam metadata</li>
                <li>• <strong class="text-white">Visual Clues:</strong> Bahasa, arsitektur, tanda jalan, brand lokal</li>
            </ul>
        </div>
        
        <div class="bg-gray-900/50 rounded p-4">
            <h5 class="text-white font-bold mb-3">📍 Geolocation Techniques:</h5>
            <div class="text-sm text-gray-400 space-y-3">
                <div>
                    <p class="text-white font-bold mb-1">1. Analyze Visual Elements:</p>
                    <ul class="ml-4 space-y-1">
                        <li>• Architecture style</li>
                        <li>• Street signs & language</li>
                        <li>• Vegetation & climate</li>
                        <li>• Vehicle license plates</li>
                    </ul>
                </div>
                <div>
                    <p class="text-white font-bold mb-1">2. Reverse Image Search:</p>
                    <ul class="ml-4 space-y-1">
                        <li>• Upload ke Google Images</li>
                        <li>• Cari hasil yang mirip</li>
                        <li>• Baca caption/description</li>
                    </ul>
                </div>
                <div>
                    <p class="text-white font-bold mb-1">3. Cross-reference:</p>
                    <ul class="ml-4 space-y-1">
                        <li>• Konfirmasi dengan Google Maps Street View</li>
                        <li>• Match dengan sudut foto yang sama</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';
}

$conn->close();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">
            🔍 <?php echo htmlspecialchars($challenge['title']); ?>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <?php
            $diff_colors = ['easy' => 'green', 'medium' => 'yellow', 'hard' => 'red'];
            $color = $diff_colors[$challenge['difficulty']];
            ?>
            <span class="text-<?php echo $color; ?>-400">● <?php echo ucfirst($challenge['difficulty']); ?></span>
            <span class="text-gray-500">OSINT</span>
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
        <div class="bg-purple-900/20 border border-purple-500/50 rounded p-3 text-sm">
            <span class="text-purple-400 font-bold">🎯 Objective:</span>
            <span class="text-gray-400"> Gunakan teknik Open Source Intelligence (OSINT) untuk menemukan informasi tersembunyi dan dapatkan flag!</span>
        </div>
    </div>

    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 mb-6">
        <h3 class="text-white font-bold mb-4">🎯 Challenge Area</h3>
        <?php echo $challenge_content; ?>
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