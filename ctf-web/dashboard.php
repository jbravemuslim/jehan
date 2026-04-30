<?php
$page_title = "Dashboard - CTF Platform";
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get user stats
$user_stats = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Get challenges
$challenges = $conn->query("SELECT * FROM challenges ORDER BY difficulty, points");

// Get solved challenges
$solved = $conn->query("SELECT challenge_id FROM submissions WHERE user_id = $user_id AND is_correct = 1");
$solved_ids = [];
while ($row = $solved->fetch_assoc()) {
    $solved_ids[] = $row['challenge_id'];
}

// Get rank
$rank_result = $conn->query("SELECT COUNT(*) + 1 as rank FROM users WHERE score > {$user_stats['score']}");
$rank = $rank_result->fetch_assoc()['rank'];

// Close connection will be done at the end
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- User Stats -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">
            Welcome back, <span class="text-ctf-accent"><?php echo htmlspecialchars($_SESSION['username']); ?></span>! 👋
        </h1>
        <p class="text-gray-400">Ready to hack some challenges?</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6">
            <div class="text-gray-400 text-sm mb-2">Total Score</div>
            <div class="text-3xl font-bold text-ctf-accent"><?php echo $user_stats['score']; ?></div>
            <div class="text-xs text-gray-500 mt-1">points</div>
        </div>

        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6">
            <div class="text-gray-400 text-sm mb-2">Solved</div>
            <div class="text-3xl font-bold text-green-400"><?php echo count($solved_ids); ?></div>
            <div class="text-xs text-gray-500 mt-1">challenges</div>
        </div>

        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6">
            <div class="text-gray-400 text-sm mb-2">Remaining</div>
            <div class="text-3xl font-bold text-yellow-400"><?php echo $challenges->num_rows - count($solved_ids); ?></div>
            <div class="text-xs text-gray-500 mt-1">challenges</div>
        </div>

        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6">
            <div class="text-gray-400 text-sm mb-2">Rank</div>
            <div class="text-3xl font-bold text-purple-400">#<?php echo $rank; ?></div>
            <div class="text-xs text-gray-500 mt-1">position</div>
        </div>
    </div>

    <!-- Challenges List -->
    <div>
        <h2 class="text-2xl font-bold text-white mb-6">🎯 Challenges</h2>

        <div class="grid grid-cols-1 gap-4">
            <?php while ($challenge = $challenges->fetch_assoc()): ?>
                <?php 
                $is_solved = in_array($challenge['id'], $solved_ids);
                $difficulty_colors = [
                    'easy' => 'green',
                    'medium' => 'yellow',
                    'hard' => 'red'
                ];
                $color = $difficulty_colors[$challenge['difficulty']];
                
                $category_icons = [
                    'sqli' => '💉',
                    'xss' => '🍪',
                    'upload' => '📤',
                    'auth' => '🔓',
                    'misc' => '🎲',
                    'osint' => '🔍'
                ];
                $icon = $category_icons[$challenge['category']];
                ?>

                <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 <?php echo $is_solved ? 'opacity-60' : ''; ?>">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-2xl"><?php echo $icon; ?></span>
                                <h3 class="text-xl font-bold text-white">
                                    <?php echo htmlspecialchars($challenge['title']); ?>
                                </h3>
                                <?php if ($is_solved): ?>
                                <span class="bg-green-900/50 text-green-400 text-xs px-2 py-1 rounded">
                                    ✓ Solved
                                </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-400 text-sm mb-4">
                                <?php echo htmlspecialchars($challenge['description']); ?>
                            </p>

                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-<?php echo $color; ?>-400">
                                    ● <?php echo ucfirst($challenge['difficulty']); ?>
                                </span>
                                <span class="text-gray-500">
                                    <?php echo strtoupper($challenge['category']); ?>
                                </span>
                                <span class="text-ctf-accent font-bold">
                                    🏆 <?php echo $challenge['points']; ?> points
                                </span>
                            </div>
                        </div>

                        <div>
                            <?php if (!$is_solved): ?>
                            <a href="challenges/<?php echo $challenge['category']; ?>/?id=<?php echo $challenge['id']; ?>" 
                               class="bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-6 py-2 rounded font-bold transition">
                                Start Challenge
                            </a>
                            <?php else: ?>
                            <button class="bg-gray-700 text-gray-400 px-6 py-2 rounded font-bold cursor-not-allowed">
                                Completed
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php 
$conn->close();
require_once 'includes/footer.php'; 
?>