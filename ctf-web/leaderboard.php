<?php
$page_title = "Leaderboard - CTF Platform";
require_once 'config/db.php';
require_once 'includes/header.php';

$conn = getDBConnection();

// Get all users sorted by score
$leaderboard = $conn->query("
    SELECT 
        u.id,
        u.username,
        u.score,
        u.is_admin,
        COUNT(DISTINCT s.challenge_id) as solved_count,
        MAX(s.submitted_at) as last_solve
    FROM users u
    LEFT JOIN submissions s ON u.id = s.user_id AND s.is_correct = 1
    GROUP BY u.id
    ORDER BY u.score DESC, last_solve ASC
");

// Get total challenges count
$total_challenges = $conn->query("SELECT COUNT(*) as total FROM challenges")->fetch_assoc()['total'];

$conn->close();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-white mb-2">
            🏆 <span class="text-ctf-accent">Leaderboard</span>
        </h1>
        <p class="text-gray-400">Top hackers ranked by score</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-ctf-accent mb-2"><?php echo $leaderboard->num_rows; ?></div>
            <div class="text-gray-400 text-sm">Total Players</div>
        </div>
        
        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-ctf-accent mb-2"><?php echo $total_challenges; ?></div>
            <div class="text-gray-400 text-sm">Total Challenges</div>
        </div>
        
        <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg p-6 text-center">
            <?php
            $max_score = $conn->query("SELECT MAX(score) as max FROM users")->fetch_assoc()['max'];
            ?>
            <div class="text-3xl font-bold text-ctf-accent mb-2"><?php echo $max_score; ?></div>
            <div class="text-gray-400 text-sm">Highest Score</div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-ctf-dark border border-ctf-accent/20 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ctf-darker border-b border-ctf-accent/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-ctf-accent uppercase tracking-wider">
                            Rank
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-ctf-accent uppercase tracking-wider">
                            Player
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ctf-accent uppercase tracking-wider">
                            Solved
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ctf-accent uppercase tracking-wider">
                            Score
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-ctf-accent uppercase tracking-wider">
                            Last Solve
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    <?php 
                    $rank = 1;
                    $prev_score = null;
                    $actual_rank = 1;
                    
                    while ($user = $leaderboard->fetch_assoc()): 
                        // Handle tied scores
                        if ($prev_score !== null && $user['score'] < $prev_score) {
                            $actual_rank = $rank;
                        }
                        $prev_score = $user['score'];
                        
                        // Medal for top 3
                        $medal = '';
                        $rank_class = '';
                        if ($actual_rank == 1) {
                            $medal = '🥇';
                            $rank_class = 'bg-yellow-900/30';
                        } elseif ($actual_rank == 2) {
                            $medal = '🥈';
                            $rank_class = 'bg-gray-700/30';
                        } elseif ($actual_rank == 3) {
                            $medal = '🥉';
                            $rank_class = 'bg-orange-900/30';
                        }
                        
                        // Highlight current user
                        $is_current_user = (isLoggedIn() && $user['id'] == $_SESSION['user_id']);
                        $row_class = $is_current_user ? 'bg-ctf-accent/10 border-l-4 border-ctf-accent' : $rank_class;
                    ?>
                    <tr class="<?php echo $row_class; ?> hover:bg-ctf-accent/5 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl"><?php echo $medal; ?></span>
                                <span class="text-lg font-bold text-white">#<?php echo $actual_rank; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-ctf-accent/20 rounded-full flex items-center justify-center">
                                    <span class="text-lg"><?php echo $user['is_admin'] ? '👑' : '👤'; ?></span>
                                </div>
                                <div>
                                    <div class="font-bold text-white">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                        <?php if ($is_current_user): ?>
                                        <span class="text-xs text-ctf-accent ml-2">(You)</span>
                                        <?php endif; ?>
                                        <?php if ($user['is_admin']): ?>
                                        <span class="text-xs text-yellow-400 ml-2">(Admin)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm">
                                <span class="text-white font-bold"><?php echo $user['solved_count']; ?></span>
                                <span class="text-gray-500">/ <?php echo $total_challenges; ?></span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5 mt-2">
                                <?php 
                                $progress = ($total_challenges > 0) ? ($user['solved_count'] / $total_challenges * 100) : 0;
                                ?>
                                <div class="bg-ctf-accent h-1.5 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-2xl font-bold text-ctf-accent"><?php echo $user['score']; ?></span>
                            <span class="text-gray-500 text-sm ml-1">pts</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-400">
                            <?php 
                            if ($user['last_solve']) {
                                $time_ago = time() - strtotime($user['last_solve']);
                                if ($time_ago < 60) {
                                    echo "Just now";
                                } elseif ($time_ago < 3600) {
                                    echo floor($time_ago / 60) . "m ago";
                                } elseif ($time_ago < 86400) {
                                    echo floor($time_ago / 3600) . "h ago";
                                } else {
                                    echo floor($time_ago / 86400) . "d ago";
                                }
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php 
                    $rank++;
                    endwhile; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Players Message -->
    <?php if ($leaderboard->num_rows == 0): ?>
    <div class="text-center py-12">
        <div class="text-6xl mb-4">🏆</div>
        <h3 class="text-xl font-bold text-white mb-2">No Players Yet</h3>
        <p class="text-gray-400">Be the first to solve challenges!</p>
    </div>
    <?php endif; ?>

    <!-- Call to Action -->
    <?php if (isLoggedIn()): ?>
    <div class="mt-8 text-center">
        <a href="dashboard.php" class="inline-block bg-ctf-accent hover:bg-ctf-accent-dark text-ctf-darker px-8 py-3 rounded-lg font-bold transition">
            🎯 Solve More Challenges
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>