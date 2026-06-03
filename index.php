<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: views/login.php');
    exit;
}

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/app/models/video.php';

$database = new Database();
$connection = $database->connect();
$videoModel = new Video($connection);

// Haal alle geuploade videos op voor de homepagina.
$videos = $videoModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>index</title>
  <link rel="stylesheet" href="css/styles.css">
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
      <a class="avatar" href="views/account.php" aria-label="Account"></a>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <a class="side-item active" href="index.php">Home</a>
      <a class="side-item" href="views/subscriptions.php">Subscriptions</a>
      <a class="side-item" href="views/library.php">Library</a>
      <a class="side-item" href="views/history.php">History</a>
    </aside>

    <main class="content">
      <h2 class="section-title">Recommended</h2>
      <?php if (empty($videos)): ?>
        <p class="empty-message">No videos uploaded yet.</p>
      <?php else: ?>
        <div class="video-grid">
          <?php foreach ($videos as $uploadedVideo): ?>
            <?php
              // Maak paden voor de thumbnail en de aparte kijkpagina.
              $file = $uploadedVideo['filename'];
              $videoPath = 'uploads/videos/' . $file;
              $watchPath = 'views/video.php?id=' . (int) $uploadedVideo['id'];
            ?>
            <a class="video-card video-card-link" href="<?= htmlspecialchars($watchPath, ENT_QUOTES, 'UTF-8') ?>">
              <video class="video-player video-thumbnail" preload="metadata" muted>
                <source src="<?= htmlspecialchars($videoPath, ENT_QUOTES, 'UTF-8') ?>">
              </video>
              <h3 class="video-title"><?= htmlspecialchars($uploadedVideo['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <?php if (!empty($uploadedVideo['description'])): ?>
                <p class="video-description"><?= htmlspecialchars($uploadedVideo['description'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>

</body>
</html>
