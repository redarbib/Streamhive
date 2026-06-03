<?php
session_start();

$registerError = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>register</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <header class="topbar">
    <div class="top-left">
      <div class="burger" aria-hidden="true">☰</div>
      <div class="logo">STREAMHIVE</div>
    </div>
  </header>

  <main class="login-page">
    <section class="login-container" aria-labelledby="register-title">
      <h1 id="register-title">Create account</h1>
      <p class="login-subtitle">Join StreamHive and start watching</p>
      <?php if ($registerError !== ''): ?>
        <p class="login-error"><?= htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>
      <form action="../app/controllers/authController.php" method="POST">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
          <label for="email">Email address</label>
          <input class="form-input" type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm password</label>
          <input class="form-input" type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button class="button" type="submit">Create account</button>
      </form>
      <div class="login-divider"></div>
      <p class="create-account">Already have an account? <a href="login.php">Sign in</a></p>
    </section>
  </main>
</body>
</html>
