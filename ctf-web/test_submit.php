<?php
require_once 'config/db.php';

// Simulate login
$_SESSION['user_id'] = 1; // Ganti dengan ID user yang valid
$_SESSION['username'] = 'admin';
$_SESSION['score'] = 0;

echo "<h1>Test Flag Submission</h1>";

// Test data
$test_challenge_id = 1; // SQL Injection challenge
$test_flag = "FLAG{sql_1nj3ct10n_ez}";

echo "<form method='POST' action='submit_flag.php'>";
echo "<input type='hidden' name='challenge_id' value='$test_challenge_id'>";
echo "<input type='text' name='flag' value='$test_flag' style='padding: 10px; width: 300px;'>";
echo "<button type='submit' style='padding: 10px 20px; margin-left: 10px;'>Submit Flag</button>";
echo "</form>";

echo "<hr>";
echo "<h2>Expected Response:</h2>";
echo "<pre>";
echo json_encode([
    'success' => true,
    'message' => 'Correct flag! +100 points',
    'points' => 100
], JSON_PRETTY_PRINT);
echo "</pre>";
?>