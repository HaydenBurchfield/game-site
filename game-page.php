<?php

require_once 'User.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user = new User();
$user->populate($_SESSION['user_id']);


$games = [
    [
        'id' => 1,
        'name' => 'Traffic Jam 3D',
        'image' => '🏎️',
        'description' => 'Driving Game',
        'url' => 'games/traffic-jam-3d.php'
    ],
    [
        'id' => 2,
        'name' => 'Test Game',
        'image' => '😁',
        'description' => 'Test Game',
        'url' => 'games/traffic-jam-3d.php'
    ]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Dashboard</title>
    <link rel="stylesheet" href="style/game-page.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="nav-bar">
        <div class="nav-left">
        </div>
        <div class="nav-right">
            <a href="settings.php" class="nav-btn">⚙️ Settings</a>
            <a href="logout.php" class="nav-btn">🔒 Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Game Center</h1>
            <p>Choose your adventure, <?=$user->username?>!</p>
        </div>

        <div class="games-grid">
            <?php foreach ($games as $game): ?>
                <a href="<?php echo htmlspecialchars($game['url']); ?>" class="game-card">
                    <span class="game-icon"><?php echo $game['image']; ?></span>
                    <div class="game-name"><?php echo htmlspecialchars($game['name']); ?></div>
                    <div class="game-description"><?php echo htmlspecialchars($game['description']); ?></div>
                    <span class="play-btn">Play Now</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Optional: Add click animation
        document.querySelectorAll('.game-card').forEach(card => {
            card.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });
    </script>
</body>
</html>