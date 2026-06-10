<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'] ?? 'Account';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="topbar">
    <div class="top-left">
      <a class="logo" href="../index.php">STREAMHIVE</a>
    </div>
    <form class="search" action="../index.php" method="GET">
      <input type="search" name="search" placeholder="Search videos...">
      <button type="submit">Search</button>
    </form>
    <div class="top-right">
      <a class="logout-link" href="../app/controllers/logoutController.php">Logout</a>
      <a class="avatar active-avatar" href="account.php" aria-label="Account"></a>
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar">
      <a class="side-item" href="../index.php">Home</a>
      <a class="side-item" href="subscriptions.php">Subscriptions</a>
      <a class="side-item" href="library.php">Library</a>
      <a class="side-item" href="history.php">History</a>
    </aside>

    <main class="content">
      <h2 class="section-title">Account</h2>
      <p class="account-email"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
      <a class="button upload-link" href="Upload.php">Upload Video</a>
    </main>
  </div>
</body>
</html>
