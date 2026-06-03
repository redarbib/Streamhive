<?php
require_once __DIR__ . '/../core/Database.php';

$database = new Database();
$connection = $database->connect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Video</title>
  <link rel="stylesheet" href="../css/uploadstyles.css">
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
    <div class="top-right"></div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <div class="side-item active">Home</div>
      <div class="side-item">Subscriptions</div>
      <div class="side-item">Library</div>
      <div class="side-item">History</div>
    </aside>

    <main class="content">
      <h2 class="section-title">Upload Video</h2>
      <div class="upload-container">
        <p>Drag & drop your video file here</p>
        <p class="upload-or">or</p>
        <button class="button">Select Video</button>
      </div>
      <form class="upload-form" action="../app/controllers/videoController.php" method="POST">
        <div class="form-group">
          <label for="title">Title:</label>
          <input class="form-input" type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea class="form-input" id="description" name="description"></textarea>
        </div>
        <div class="form-group">
          <label for="video">Category:</label>
          <select class="form-dropdown" id="video" name="video" required>
            <option value="">Select a category</option>
            <option value="gaming">Gaming</option>
            <option value="music">Music</option>
            <option value="movies">Movies</option>
          </select>
        </div>
        <button class="button" type="submit">Upload Video</button>
      </form>
    </main>
  </div>

</body>
</html>
