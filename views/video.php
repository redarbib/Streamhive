<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/models/video.php';

$videoId = (int) ($_GET['id'] ?? 0);
$database = new Database();
$connection = $database->connect();
$videoModel = new Video($connection);
$currentVideo = $videoModel->findById($videoId);

// Als de video niet bestaat, terug naar home.
if (!$currentVideo) {
    header('Location: ../index.php');
    exit;
}

// Tel een view zodra de kijkpagina opent.
$videoModel->addView($videoId);

$videoPath = '../uploads/videos/' . rawurlencode($currentVideo['filename']);
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
      <h2 class="watch-title"><?= htmlspecialchars($currentVideo['title'] ?? 'Untitled video', ENT_QUOTES, 'UTF-8') ?></h2>
      <?php if (!empty($currentVideo['description'])): ?>
        <p class="watch-description"><?= htmlspecialchars($currentVideo['description'], ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
