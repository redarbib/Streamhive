<?php
require_once __DIR__ . '/core/Database.php';

$database = new Database();
$connection = $database->connect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wireframes & Mockups</title>
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
      <h2 class="section-title">Recommended</h2>
    </main>
  </div>

</body>
</html>
