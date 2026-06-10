<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/models/video.php';
require_once __DIR__ . '/../app/models/like.php';
require_once __DIR__ . '/../app/models/comment.php';

$videoId = (int) ($_GET['id'] ?? 0);
$database = new Database();
$connection = $database->connect();
$videoModel = new Video($connection);
$likeModel = new Like($connection);
$commentModel = new Comment($connection);
$currentVideo = $videoModel->findById($videoId);

// Als de video niet bestaat, terug naar home.
if (!$currentVideo) {
    header('Location: ../index.php');
    exit;
}

$videoPath = '../uploads/videos/' . rawurlencode($currentVideo['filename']);
$likes = $likeModel->countForVideo($videoId);
$likedByUser = $likeModel->userLikedVideo((int) $_SESSION['user_id'], $videoId);
$comments = $commentModel->getForVideo($videoId);
$commentError = $_SESSION['comment_error'] ?? '';
unset($_SESSION['comment_error']);

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
      <a class="logo" href="../index.php">STREAMHIVE</a>
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

      <section class="comments-section">
        <h3 class="comments-title">Comments</h3>
        <?php if ($commentError !== ''): ?>
          <p class="comment-error"><?= htmlspecialchars($commentError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form class="comment-form" action="../app/controllers/commentController.php" method="POST">
          <input type="hidden" name="video_id" value="<?= (int) $currentVideo['id'] ?>">
          <textarea class="form-input comment-input" name="content" placeholder="Write a comment..." required></textarea>
          <button class="button" type="submit">Place comment</button>
        </form>

        <?php if (empty($comments)): ?>
          <p class="empty-message">No comments yet.</p>
        <?php else: ?>
          <div class="comment-list">
            <?php foreach ($comments as $comment): ?>
              <?php
                // Gebruik de tekst voor de @ als simpele accountnaam.
                $accountName = explode('@', $comment['email'])[0] ?: $comment['email'];
              ?>
              <article class="comment-card">
                <div class="comment-avatar" aria-hidden="true"></div>
                <div class="comment-body">
                  <strong class="comment-name"><?= htmlspecialchars($accountName, ENT_QUOTES, 'UTF-8') ?></strong>
                  <p class="comment-text"><?= nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
