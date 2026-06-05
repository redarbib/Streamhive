<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../core/Database.php';

$database = new Database();
$connection = $database->connect();

$uploadError = $_SESSION['upload_error'] ?? '';
$uploadSuccess = $_SESSION['upload_success'] ?? '';

// Meldingen maar een keer tonen na redirect.
unset($_SESSION['upload_error']);
unset($_SESSION['upload_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Video</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="upload-page">
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

    <main class="content upload-content">
      <h2 class="section-title">Upload Video</h2>
      <?php if ($uploadError !== ''): ?>
        <p class="upload-error"><?= htmlspecialchars($uploadError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <?php if ($uploadSuccess !== ''): ?>
        <p class="upload-success"><?= htmlspecialchars($uploadSuccess, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <!-- Formulier stuurt titel, beschrijving en videobestand naar de controller. -->
      <form class="upload-form" action="../app/controllers/videoController.php" method="POST" enctype="multipart/form-data">
        <div class="form-group file-group">
          <label class="file-upload-box" for="video">
            <span class="file-upload-icon">Upload</span>
            <span class="file-upload-title">Drag and drop your video here</span>
            <span class="file-upload-button">Select File</span>
          </label>
          <input class="file-input" type="file" id="video" name="video" accept="video/*" required>
        </div>
        <div class="form-group">
          <label for="title">Title</label>
          <input class="form-input" type="text" id="title" name="title" placeholder="Enter video title" required>
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <textarea class="form-input" id="description" name="description" placeholder="Tell viewers about your video..."></textarea>
        </div>
        <button class="button" type="submit">Upload Video</button>
      </form>
    </main>
  </div>
</body>
</html>
