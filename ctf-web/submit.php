<?php
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$challenge_id = intval($_POST['challenge_id']);
$submitted_flag = trim($_POST['flag']);
$user_id = $_SESSION['user_id'];

$conn = getDBConnection();

// Get challenge
$stmt = $conn->prepare("SELECT * FROM challenges WHERE id = ?");
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$challenge = $stmt->get_result()->fetch_assoc();

if (!$challenge) {
    echo json_encode(['success' => false, 'message' => 'Challenge not found']);
    exit();
}

// Check if already solved
$stmt = $conn->prepare("SELECT * FROM submissions WHERE user_id = ? AND challenge_id = ? AND is_correct = 1");
$stmt->bind_param("ii", $user_id, $challenge_id);
$stmt->execute();
$already_solved = $stmt->get_result()->num_rows > 0;

if ($already_solved) {
    echo json_encode(['success' => false, 'message' => 'Already solved this challenge!']);
    exit();
}

// Check flag
$is_correct = ($submitted_flag === $challenge['flag']);

// Insert submission
$stmt = $conn->prepare("INSERT INTO submissions (user_id, challenge_id, submitted_flag, is_correct) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisi", $user_id, $challenge_id, $submitted_flag, $is_correct);
$stmt->execute();

if ($is_correct) {
    // Update user score
    $stmt = $conn->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $stmt->bind_param("ii", $challenge['points'], $user_id);
    $stmt->execute();
    
    // Update session
    $_SESSION['score'] += $challenge['points'];
    
    echo json_encode([
        'success' => true, 
        'message' => 'Correct flag! +' . $challenge['points'] . ' points',
        'points' => $challenge['points']
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Wrong flag. Try again!'
    ]);
}

$stmt->close();
$conn->close();
?>