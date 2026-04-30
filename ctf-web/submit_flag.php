<?php
require_once 'config/db.php';

header('Content-Type: application/json');

// Debug logging (hapus di production)
error_log("=== FLAG SUBMISSION DEBUG ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Session: " . print_r($_SESSION, true));

if (!isLoggedIn()) {
    error_log("ERROR: Not logged in");
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("ERROR: Invalid request method");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_POST['challenge_id']) || !isset($_POST['flag'])) {
    error_log("ERROR: Missing parameters");
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$challenge_id = intval($_POST['challenge_id']);
$submitted_flag = trim($_POST['flag']);
$user_id = $_SESSION['user_id'];

error_log("Challenge ID: $challenge_id");
error_log("Submitted Flag: $submitted_flag");
error_log("User ID: $user_id");

$conn = getDBConnection();

// Get challenge
$stmt = $conn->prepare("SELECT * FROM challenges WHERE id = ?");
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$result = $stmt->get_result();
$challenge = $result->fetch_assoc();

if (!$challenge) {
    error_log("ERROR: Challenge not found - ID: $challenge_id");
    echo json_encode(['success' => false, 'message' => 'Challenge not found']);
    $stmt->close();
    $conn->close();
    exit();
}

error_log("Challenge found: " . $challenge['title']);
error_log("Correct flag: " . $challenge['flag']);

// Check if already solved
$stmt = $conn->prepare("SELECT * FROM submissions WHERE user_id = ? AND challenge_id = ? AND is_correct = 1");
$stmt->bind_param("ii", $user_id, $challenge_id);
$stmt->execute();
$already_solved = $stmt->get_result()->num_rows > 0;

if ($already_solved) {
    error_log("INFO: Already solved");
    echo json_encode(['success' => false, 'message' => 'Already solved this challenge!']);
    $stmt->close();
    $conn->close();
    exit();
}

// Check flag
$is_correct = ($submitted_flag === $challenge['flag']);
error_log("Flag match: " . ($is_correct ? 'YES' : 'NO'));

// Insert submission
$stmt = $conn->prepare("INSERT INTO submissions (user_id, challenge_id, submitted_flag, is_correct) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisi", $user_id, $challenge_id, $submitted_flag, $is_correct);

if (!$stmt->execute()) {
    error_log("ERROR: Failed to insert submission - " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    $stmt->close();
    $conn->close();
    exit();
}

error_log("Submission inserted successfully");

if ($is_correct) {
    // Update user score
    $stmt = $conn->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $stmt->bind_param("ii", $challenge['points'], $user_id);
    
    if (!$stmt->execute()) {
        error_log("ERROR: Failed to update score - " . $stmt->error);
    } else {
        error_log("Score updated: +" . $challenge['points']);
    }
    
    // Update session
    $_SESSION['score'] = ($_SESSION['score'] ?? 0) + $challenge['points'];
    
    error_log("SUCCESS: Flag correct!");
    echo json_encode([
        'success' => true, 
        'message' => 'Correct flag! +' . $challenge['points'] . ' points',
        'points' => $challenge['points']
    ]);
} else {
    error_log("FAIL: Wrong flag");
    echo json_encode([
        'success' => false, 
        'message' => 'Wrong flag. Try again!'
    ]);
}

$stmt->close();
$conn->close();
?>