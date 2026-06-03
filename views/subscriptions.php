<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscriptions</title>
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
      <a class="side-item active" href="subscriptions.php">Subscriptions</a>
      <a class="side-item" href="library.php">Library</a>
      <a class="side-item" href="history.php">History</a>
    </aside>

    <main class="content">
      <h2 class="section-title">Subscriptions</h2>
    </main>
  </div>
</body>
</html>
