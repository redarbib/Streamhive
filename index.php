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
$search = trim($_GET['search'] ?? '');

// Haal videos op voor de homepagina, eventueel gefilterd op zoektekst.
$videos = $search === '' ? $videoModel->getAll() : $videoModel->search($search);
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
      <a class="logo" href="index.php">STREAMHIVE</a>
    </div>
    <form class="search" action="index.php" method="GET">
      <input type="search" name="search" placeholder="Search videos..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit">Search</button>
    </form>
    <div class="top-right">
      <a class="logout-link" href="app/controllers/logoutController.php">Logout</a>
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
      <h2 class="section-title"><?= $search === '' ? 'Recommended' : 'Search results for "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '"' ?></h2>
      <?php if (empty($videos)): ?>
        <p class="empty-message"><?= $search === '' ? 'No videos uploaded yet.' : 'No videos found.' ?></p>
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
              <p class="video-meta"><?= number_format((int) $uploadedVideo['views']) ?> views</p>
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
