<?php

require_once '../User.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user = new User();
$user->populate($_SESSION['user_id']);


// Game information
$game_title = "Traffic Jam 3D";
$game_description = "Driving";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game_title); ?></title>
    <link rel="stylesheet" href="../style/game-playing-page.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="nav-bar">
        <div class="nav-left">
            <a href="../game-page.php" class="back-btn">← Back to Games</a>
            <span class="game-title-nav"><?php echo htmlspecialchars($game_title); ?></span>
        </div>
        <div class="nav-right">
            <a href="../game-page.php" class="nav-btn">🏠 Home</a>
            <a href="../logout.php" class="nav-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- Game Header -->
        <div class="game-header">
            <h1><?php echo htmlspecialchars($game_title); ?></h1>
            <p><?php echo htmlspecialchars($game_description); ?></p>
        </div>

        <!-- Game Container -->
        <div class="game-container" id="gameContainer">
            <div class="game-wrapper" id="gameWrapper">
                <div class="loading-screen" id="loadingScreen">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Loading game...</div>
                </div>
                <iframe 
                    id="gameFrame"
                    src = "https://unblocked-games.s3.amazonaws.com/games/2025/unity3/traffic-jam-3d/index.html"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                ></iframe>
            </div>

            <!-- Game Controls -->
            <div class="game-controls">
                <button class="control-btn primary" onclick="restartGame()">🔄 Restart</button>
                <button class="control-btn" onclick="toggleFullscreen()">⛶ Fullscreen</button>
                <button class="control-btn" onclick="toggleSound()">🔊 Sound</button>
            </div>
        </div>
    </div>

    <script>
        // Hide loading screen after iframe loads
        const gameFrame = document.getElementById('gameFrame');
        const loadingScreen = document.getElementById('loadingScreen');
        
        gameFrame.addEventListener('load', function() {
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        });

        // Restart game function
        function restartGame() {
            loadingScreen.style.display = 'flex';
            gameFrame.src = gameFrame.src;
        }

        // Fullscreen toggle
        function toggleFullscreen() {
            const gameContainer = document.getElementById('gameContainer');
            
            if (!document.fullscreenElement) {
                gameContainer.requestFullscreen().catch(err => {
                    alert(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        // Sound toggle (placeholder - implement based on your game)
        let soundEnabled = true;
        function toggleSound() {
            soundEnabled = !soundEnabled;
            const btn = event.target;
            btn.textContent = soundEnabled ? '🔊 Sound' : '🔇 Muted';
            // Add your sound control logic here
        }

        // Handle fullscreen change
        document.addEventListener('fullscreenchange', function() {
            const gameContainer = document.getElementById('gameContainer');
            if (document.fullscreenElement) {
                gameContainer.classList.add('fullscreen');
            } else {
                gameContainer.classList.remove('fullscreen');
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // F key for fullscreen
            if (e.key === 'f' || e.key === 'F') {
                toggleFullscreen();
            }
            // R key for restart
            if (e.key === 'r' || e.key === 'R') {
                restartGame();
            }
            // Escape key to exit fullscreen
            if (e.key === 'Escape' && document.fullscreenElement) {
                document.exitFullscreen();
            }
        });
    </script>
</body>
</html>