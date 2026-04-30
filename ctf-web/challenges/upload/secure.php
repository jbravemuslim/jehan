<?php
$page_title = "File Upload Challenge (Secure)";
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

$message = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // SECURE: Whitelist allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $message = 'Only images and PDF files are allowed!';
    } else {
        // SECURE: Rename file to prevent overwrite
        $new_filename = uniqid() . '.' . $ext;
        $target = __DIR__ . '/uploads/' . $new_filename;
        
        // SECURE: Check file type via MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        
        if (!in_array($mime, $allowed_mimes)) {
            $message = 'Invalid file type!';
        } else {
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $uploaded_file = 'uploads/' . $new_filename;
                $message = 'File uploaded successfully!';
            } else {
                $message = 'Upload failed!';
            }
        }
    }
}
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="../../dashboard.php" class="text-ctf-accent hover:underline text-sm mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-green-400 mb-2">
            📤 File Upload Challenge (Secure Version)
        </h1>
    </div>

    <div class="bg-green-900/30 border border-green-500/50 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <span class="text-2xl mr-3">✅</span>
            <div>
                <h4 class="text-green-400 font-bold text-sm mb-1">Secure Version</h4>
                <p class="text-gray-400 text-xs mb-2">Proteksi yang diterapkan:</p>
                <ul class="text-xs text-gray-400 space-y-1">
                    <li>• Extension whitelist (hanya jpg, png, gif, pdf)</li>
                    <li>• MIME type validation</li>
                    <li>• Filename randomization (prevent overwrite)</li>
                </ul>
                <a href="index.php?id=<?php echo $challenge_id; ?>" class="text-yellow-400 text-xs hover:underline mt-2 inline-block">
                    👉 Lihat vulnerable version
                </a>
            </div>
        </div>
    </div>

    <div class="bg-ctf-dark border border-green-500/20 rounded-lg p-6 mb-6">
        <div class="bg-ctf-darker border border-gray-700 rounded-lg p-8">
            <form method="POST" enctype="multipart/form-data" class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select File (Images/PDF only)</label>
                    <input 
                        type="file" 
                        name="file" 
                        accept=".jpg,.jpeg,.png,.gif,.pdf"
                        class="w-full px-4 py-2 bg-black/50 border border-gray-700 rounded focus:outline-none focus:border-green-400 text-white"
                        required
                    >
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded transition">
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

    <div class="bg-ctf-darker border border-gray-700 rounded-lg p-4">
        <h4 class="text-white font-bold text-sm mb-3">📝 Perbedaan Kode:</h4>
        
        <div class="mb-4">
            <p class="text-red-400 text-xs font-bold mb-1">❌ Vulnerable:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                $filename = $_FILES['file']['name'];<br>
                move_uploaded_file($tmp, 'uploads/' . $filename);
            </code>
        </div>
        
        <div>
            <p class="text-green-400 text-xs font-bold mb-1">✅ Secure:</p>
            <code class="text-xs text-gray-300 bg-black/50 p-2 rounded block">
                $allowed = ['jpg', 'png', 'gif'];<br>
                $ext = pathinfo($filename, PATHINFO_EXTENSION);<br>
                if (!in_array($ext, $allowed)) die('Invalid!');<br>
                $new_name = uniqid() . '.' . $ext;
            </code>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>