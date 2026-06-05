<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/models/video.php';
require_once __DIR__ . '/../app/models/like.php';

$videoId = (int) ($_GET['id'] ?? 0);
$database = new Database();
$connection = $database->connect();
$videoModel = new Video($connection);
$likeModel = new Like($connection);
$currentVideo = $videoModel->findById($videoId);

// Als de video niet bestaat, terug naar home.
if (!$currentVideo) {
    header('Location: ../index.php');
    exit;
}

$videoPath = '../uploads/videos/' . rawurlencode($currentVideo['filename']);
$likes = $likeModel->countForVideo($videoId);
$likedByUser = $likeModel->userLikedVideo((int) $_SESSION['user_id'], $videoId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($currentVideo['title'] ?? 'Video', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="topbar">
    <div class="top-left">
      <div class="burger">☰</div>
      <div class="logo">STREAMHIVE</div>
    </div>
    <div class="search">
      <span>Search videos...</span>
    </div>
    <div class="top-right">
      <a class="logout-link" href="../app/controllers/logoutController.php">Logout</a>
      <a class="avatar" href="account.php" aria-label="Account"></a>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <a class="side-item" href="../index.php">Home</a>
      <a class="side-item" href="subscriptions.php">Subscriptions</a>
      <a class="side-item" href="library.php">Library</a>
      <a class="side-item" href="history.php">History</a>
    </aside>

    <main class="content watch-content">
      <video class="watch-player" controls autoplay>
        <source src="<?= htmlspecialchars($videoPath, ENT_QUOTES, 'UTF-8') ?>">
      </video>
      <div class="watch-header">
        <h2 class="watch-title"><?= htmlspecialchars($currentVideo['title'] ?? 'Untitled video', ENT_QUOTES, 'UTF-8') ?></h2>
        <form action="../app/controllers/likeController.php" method="POST">
          <input type="hidden" name="video_id" value="<?= (int) $currentVideo['id'] ?>">
          <button class="like-button <?= $likedByUser ? 'liked' : '' ?>" type="submit">
            <span class="like-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M12 21s-7-4.4-9.3-8.2C.9 9.8 2.2 5.5 5.7 4.5 8 3.8 10.3 5 12 7.1c1.7-2.1 4-3.3 6.3-2.6 3.5 1 4.8 5.3 3 8.3C19 16.6 12 21 12 21z"/>
              </svg>
            </span>
            <span><?= $likedByUser ? 'Liked' : 'Like' ?></span>
            <span>(<?= $likes ?>)</span>
          </button>
        </form>
      </div>
      <?php if (!empty($currentVideo['description'])): ?>
        <p class="watch-description"><?= htmlspecialchars($currentVideo['description'], ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
