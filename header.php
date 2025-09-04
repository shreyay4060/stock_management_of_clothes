<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['name'] ?? null;
$role = $_SESSION['role'] ?? null;
?>
<header class="navbar">
  <a class="brand" href="index.php">Clothes Stock</a>

  <nav class="navlinks" id="navlinks">
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Home</a>
    <a href="about.php" class="<?= basename($_SERVER['PHP_SELF'])=='about.php'?'active':'' ?>">About</a>
    <a href="services.php" class="<?= basename($_SERVER['PHP_SELF'])=='services.php'?'active':'' ?>">Services</a>
    <a href="retailer.php" class="<?= basename($_SERVER['PHP_SELF'])=='retailer.php'?'active':'' ?>">Retailer</a>
    <?php if ($role === "admin"): ?>
      <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF'])=='admin.php'?'active':'' ?>">Admin</a>
    <?php endif; ?>
  </nav>

  <div class="nav-actions">
    <a class="btn-outline" href="cart.php">🛒 Cart</a>
    <?php if ($user): ?>
    <a id="profileBadge" class="badge" href="profile.php">
  <?= htmlspecialchars(explode(" ", $user)[0]) ?> (<?= htmlspecialchars($role) ?>)
</a>

      <a href="logout.php" id="logoutBtn" class="btn-primary">Logout</a>
    <?php else: ?>
      <button id="btnShowLogin" class="btn-primary">Login</button>
      <button id="btnShowSignup" class="btn-ghost">Sign up</button>
    <?php endif; ?>
  </div>
</header>

<?php if (!$user): ?>
  <!-- ========== Login Modal ========== -->
  <div id="loginModal" class="modal hidden">
    <div class="modal-content">
      <span class="close" data-close="loginModal">&times;</span>
      <h2>Login</h2>
      <form id="loginForm">
        <input type="email" id="loginEmail" name="email" placeholder="Email" required autocomplete="username">
        <input type="password" id="loginPass" name="password" placeholder="Password" required autocomplete="current-password">
        <button type="submit" id="btnLogin" class="btn-primary">Login</button>
        <p>Don't have an account? <a href="#" id="openSignup">Sign up</a></p>
        <small id="loginMsg"></small>
      </form>
    </div>
  </div>

  <!-- ========== Signup Modal ========== -->
  <div id="signupModal" class="modal hidden">
    <div class="modal-content">
      <span class="close" data-close="signupModal">&times;</span>
      <h2>Sign Up</h2>
      <form id="signupForm">
        <input type="text" id="signupName" name="name" placeholder="Full Name" required autocomplete="name">
        <input type="email" id="signupEmail" name="email" placeholder="Email" required autocomplete="username">
        <input type="password" id="signupPass" name="password" placeholder="Password" required autocomplete="new-password">
        <button type="submit" id="btnSignup" class="btn-primary">Sign Up</button>
        <p>Have an account? <a  href="#" id="openLogin">Login</a></p>
        <small id="signupMsg"></small>
      </form>
    </div>
  </div>
<?php endif; ?>
