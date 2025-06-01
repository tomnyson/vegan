<header>
  <div class="header-container">
    <div class="logo-container">
      <i class="fas fa-leaf"></i>
      <h2>Hương Từ Bi</h2>
    </div>

    <!-- Mobile menu toggle button -->
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>

    <!-- Desktop Navigation -->
    <nav class="desktop-menu">
      <a href="index.php" class="nav-link active">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
      </a>
      <a href="products.php" class="nav-link">
        <i class="fas fa-utensils"></i>
        <span>Thực đơn</span>
      </a>
      <a href="AboutUs.php" class="nav-link">
        <i class="fas fa-info-circle"></i>
        <span>Về chúng tôi</span>
      </a>
      <a href="Cart.php" class="nav-link">
        <i class="fas fa-shopping-cart"></i>
        <span>Giỏ hàng</span>
        <span class="cart-count" id="cart-count">0</span>
      </a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php" class="nav-link"><i class="fas fa-user-edit"></i> Hồ sơ</a>
        <a href="orders.php" class="nav-link"><i class="fas fa-list-alt"></i> Đơn hàng</a>
        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
      <?php else: ?>
        <a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
        <a href="register.php" class="nav-link"><i class="fas fa-user-plus"></i> Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-overlay"></div>

<!-- Mobile Menu -->
<div class="mobile-menu">
  <button class="mobile-menu-close" onclick="toggleMobileMenu()">
    <i class="fas fa-times"></i>
  </button>
  <a href="index.php">
    <i class="fas fa-home"></i> Trang chủ
  </a>
  <a href="products.php">
    <i class="fas fa-utensils"></i> Thực đơn
  </a>
  <a href="AboutUs.php">
    <i class="fas fa-info-circle"></i> Về chúng tôi
  </a>
  <a href="Cart.php">
    <i class="fas fa-shopping-cart"></i> Giỏ hàng
    <span class="cart-count" id="cart-count-mobile">0</span>
  </a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="profile.php"><i class="fas fa-user-edit"></i> Hồ sơ</a>
    <a href="orders.php"><i class="fas fa-list-alt"></i> Đơn hàng</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
  <?php else: ?>
    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
    <a href="register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
  <?php endif; ?>
</div>

<script>
  // Mobile menu toggle functionality is now handled in assets/js/script.js
  // This ensures no conflicts with other JavaScript functions
</script>
</style>