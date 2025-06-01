<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký - Nhà Hàng Chay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/style.css">
  <style>
    /* Register page specific styles */
    .register-wrapper {
      background: var(--bg-light);
      padding: 60px 20px;
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .register-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      padding: 40px;
      width: 100%;
      max-width: 500px;
      text-align: center;
    }

    .register-container h2 {
      color: var(--primary-color);
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
    }

    .register-container h2:after {
      content: "";
      position: absolute;
      width: 60px;
      height: 3px;
      background: var(--accent-color);
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }

    .register-container h2 i {
      margin-right: 10px;
      color: var(--accent-color);
    }

    .register-form {
      text-align: left;
      margin-top: 40px;
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--text-dark);
      font-weight: 600;
      font-size: 1rem;
    }

    .form-group label i {
      margin-right: 8px;
      color: var(--primary-color);
      width: 16px;
    }

    .form-group input {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #fff;
      color: var(--text-dark);
    }

    .form-group input:focus {
      border-color: var(--primary-color);
      outline: none;
      box-shadow: 0 0 0 3px rgba(85, 124, 62, 0.1);
    }

    .form-row {
      display: flex;
      gap: 15px;
    }

    .form-row .form-group {
      flex: 1;
    }

    .password-requirements {
      background: var(--bg-accent);
      padding: 15px;
      border-radius: 8px;
      margin-top: 10px;
      font-size: 0.9rem;
      border: 1px solid #e8e8e8;
    }

    .password-requirements h4 {
      color: var(--primary-color);
      margin: 0 0 10px 0;
      font-size: 1rem;
    }

    .password-requirements ul {
      margin: 0;
      padding-left: 20px;
      color: var(--text-dark);
    }

    .password-requirements li {
      margin-bottom: 5px;
    }

    .btn-register {
      width: 100%;
      padding: 15px 20px;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-register:hover {
      background: #4a6b35;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .login-link {
      background: var(--bg-accent);
      padding: 20px;
      border-radius: 10px;
      margin-top: 30px;
      text-align: center;
      border: 1px solid #e8e8e8;
    }

    .login-link p {
      margin: 0 0 10px 0;
      color: var(--text-dark);
    }

    .login-link a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: bold;
      transition: color 0.3s ease;
    }

    .login-link a:hover {
      color: var(--accent-color);
    }

    .social-register {
      margin-top: 30px;
      padding-top: 25px;
      border-top: 1px solid #e8e8e8;
    }

    .social-register p {
      text-align: center;
      margin-bottom: 15px;
      color: var(--text-dark);
      font-size: 0.95rem;
    }

    .social-buttons {
      display: flex;
      gap: 15px;
    }

    .social-btn {
      flex: 1;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      background: #fff;
      color: var(--text-dark);
      text-decoration: none;
      font-weight: 600;
      text-align: center;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .social-btn:hover {
      border-color: var(--primary-color);
      background: var(--bg-accent);
    }

    .social-btn.facebook {
      color: #1877f2;
    }

    .social-btn.google {
      color: #db4437;
    }

    /* Responsive styles */
    @media (max-width: 600px) {
      .register-wrapper {
        padding: 30px 15px;
      }
      
      .register-container {
        padding: 30px 20px;
      }
      
      .register-container h2 {
        font-size: 1.7rem;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }
      
      .social-buttons {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="register-wrapper">
  <div class="register-container">
    <h2><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h2>
    <?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0): ?>
      <div class="error-messages" style="background: #ffe0e0; padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #a94442; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-left: 20px;">
          <?php foreach ($_SESSION['errors'] as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; unset($_SESSION['errors']); ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form class="register-form" method="POST" action="register_process.php">
      <div class="form-row">
        <div class="form-group">
          <label for="first-name">
            <i class="fas fa-user"></i> Họ Và Tên
          </label>
          <input type="text" id="first-name" name="name" required placeholder="Nhập họ và tên của bạn">
        </div>
      </div>
      
      <div class="form-group">
        <label for="email">
          <i class="fas fa-envelope"></i> Email
        </label>
        <input type="email" id="email" name="email" required placeholder="Nhập địa chỉ email của bạn">
      </div>
      
      <div class="form-group">
        <label for="phone">
          <i class="fas fa-phone"></i> Số điện thoại
        </label>
        <input type="tel" id="phone" name="phone" required placeholder="Nhập số điện thoại">
      </div>
      
      <div class="form-group">
        <label for="password">
          <i class="fas fa-lock"></i> Mật khẩu
        </label>
        <input type="password" id="password" name="password" required placeholder="Tạo mật khẩu">
        
        <div class="password-requirements">
          <h4><i class="fas fa-info-circle"></i> Yêu cầu mật khẩu:</h4>
          <ul>
            <li>Ít nhất 8 ký tự</li>
            <li>Chứa ít nhất 1 chữ hoa</li>
            <li>Chứa ít nhất 1 số</li>
            <li>Chứa ít nhất 1 ký tự đặc biệt</li>
          </ul>
        </div>
      </div>
      
      <div class="form-group">
        <label for="confirm-password">
          <i class="fas fa-lock"></i> Xác nhận mật khẩu
        </label>
        <input type="password" id="confirm-password" name="confirm_password" required placeholder="Nhập lại mật khẩu">
      </div>
      
      <button type="submit" class="btn-register">
        <i class="fas fa-user-plus"></i> Đăng ký tài khoản
      </button>
    </form>

    <div class="login-link">
      <p>Đã có tài khoản?</p>
      <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập ngay</a>
    </div>

    <div class="social-register">
      <p>Hoặc đăng ký bằng:</p>
      <div class="social-buttons">
        <a href="#" class="social-btn facebook">
          <i class="fab fa-facebook-f"></i> Facebook
        </a>
        <a href="#" class="social-btn google">
          <i class="fab fa-google"></i> Google
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="./assets/js/script.js"></script>
</body>
</html>