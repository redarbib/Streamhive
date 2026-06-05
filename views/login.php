<?php
session_start();

$loginError = $_SESSION['login_error'] ?? '';
$loginSuccess = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_error']);
unset($_SESSION['login_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>login</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <header class="topbar">
    <div class="top-left">
      <div class="logo">STREAMHIVE</div>
    </div>
  </header>

  <main class="login-page">
    <section class="login-container" aria-labelledby="login-title">
      <h1 id="login-title">Sign in</h1>
      <p class="login-subtitle">Welcome back sign in to continue</p>
      <?php if ($loginError !== ''): ?>
        <p class="login-error"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <?php if ($loginSuccess !== ''): ?>
        <p class="login-success"><?= htmlspecialchars($loginSuccess, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <form action="../app/controllers/authController.php" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
          <label for="username">Email address</label>
          <input class="form-input" type="text" id="username" name="username" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" required>
        </div>
        <button class="button" type="submit">Sign in</button>
      </form>
      <div class="login-divider"></div>
      <p class="create-account">Dont have an account? <a href="register.php">Create one</a></p>
    </section>
  </main>
</body>
</html>
