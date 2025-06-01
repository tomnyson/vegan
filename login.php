<?php
session_start();
require_once 'helpers/database.php';

$error_message = '';
$success_message = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
  $success_message = 'Bạn đã đăng xuất thành công.';
}

// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Validate input
  if (empty($email) || empty($password)) {
    $error_message = 'Vui lòng nhập đầy đủ email và mật khẩu.';
  } else {
    try {
      // Check user credentials
      $user = db_query_one(
        "SELECT * FROM users WHERE email = ? AND status = 'active'",
        [$email]
      );

      if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];

        // Redirect to intended page or home
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . $redirect);
        exit();
      } else {
        $error_message = 'Email hoặc mật khẩu không đúng.';
      }
    } catch (Exception $e) {
      $error_message = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Đăng nhập - Nhà Hàng Chay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/style.css">
  <style>
    .login-wrapper {
      background: #f8f7f3;
      padding: 60px 20px;
      min-height: calc(100vh - 200px);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 40px;
      width: 100%;
      max-width: 480px;
      text-align: center;
    }

    .login-container h2 {
      color: #557c3e;
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: bold;
      position: relative;
    }

    .login-container h2:after {
      content: "";
      position: absolute;
      width: 60px;
      height: 3px;
      background: #d4af37;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }

    .login-form {
      text-align: left;
      margin-top: 30px;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-weight: 600;
      font-size: 1rem;
    }

    .form-group label i {
      margin-right: 8px;
      color: #557c3e;
    }

    .form-group input {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #fff;
    }

    .form-group input:focus {
      border-color: #557c3e;
      outline: none;
      box-shadow: 0 0 0 3px rgba(85, 124, 62, 0.1);
    }

    .btn-login {
      width: 100%;
      padding: 14px 20px;
      background: #557c3e;
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

    .btn-login:hover {
      background: #4a6b35;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .forgot-password {
      margin-top: 15px;
      text-align: right;
    }

    .forgot-password a {
      color: #557c3e;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .register-link {
      margin-top: 30px;
      background: #f2f1eb;
      padding: 20px;
      border-radius: 10px;
    }

    .register-link p {
      margin: 0 0 10px 0;
      color: #333;
    }

    .register-link a {
      color: #557c3e;
      font-weight: bold;
      text-decoration: none;
    }

    .social-login {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }

    .social-login p {
      margin-bottom: 15px;
      color: #444;
    }

    .social-buttons {
      display: flex;
      gap: 15px;
    }

    .social-btn {
      flex: 1;
      padding: 12px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      text-align: center;
      background: #fff;
      font-weight: 600;
      transition: all 0.3s ease;
      color: #444;
      text-decoration: none;
    }

    .social-btn:hover {
      border-color: #557c3e;
      background: #f9f9f5;
    }

    .social-btn.facebook {
      color: #1877f2;
    }

    .social-btn.google {
      color: #db4437;
    }

    @media (max-width: 600px) {
      .login-wrapper {
        padding: 30px 15px;
      }

      .login-container {
        padding: 30px 20px;
      }

      .social-buttons {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <div class="login-wrapper">
    <div class="login-container">
      <h2><i class="fas fa-sign-in-alt"></i> Đăng nhập</h2>

      <?php if ($error_message): ?>
        <div class="login-alert error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <?php if ($success_message): ?>
        <div class="login-alert success">
          <i class="fas fa-check-circle"></i>
          <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>

      <form class="login-form" method="POST" action="">
        <div class="form-group">
          <label for="email">
            <i class="fas fa-envelope"></i> Email
          </label>
          <input type="email" id="email" name="email" required
            placeholder="Nhập địa chỉ email của bạn"
            value="<?php echo htmlspecialchars($email ?? ''); ?>">
        </div>

        <div class="form-group">
          <label for="password">
            <i class="fas fa-lock"></i> Mật khẩu
          </label>
          <input type="password" id="password" name="password" required
            placeholder="Nhập mật khẩu">
        </div>

        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Đăng nhập
        </button>

        <div class="forgot-password">
          <a href="#"><i class="fas fa-question-circle"></i> Quên mật khẩu?</a>
        </div>
      </form>

      <div class="register-link">
        <p>Chưa có tài khoản?</p>
        <a href="register.php"><i class="fas fa-user-plus"></i> Đăng ký ngay</a>
      </div>

      <div class="social-login">
        <p>Hoặc đăng nhập bằng:</p>
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

  <script>
    // Add loading state to login button
    document.querySelector('.login-form').addEventListener('submit', function(e) {
      const button = this.querySelector('.btn-login');
      button.classList.add('loading');
      button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
    });

    // Enhanced form validation
    document.addEventListener('DOMContentLoaded', function() {
      const emailInput = document.getElementById('email');
      const passwordInput = document.getElementById('password');

      // Email validation
      emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && !emailRegex.test(email)) {
          this.style.borderColor = '#e74c3c';
        } else {
          this.style.borderColor = '#e1e5e9';
        }
      });

      // Auto-focus on page load
      emailInput.focus();

      // Enter key support
      passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          document.querySelector('.btn-login').click();
        }
      });
    });
  </script>

</body>

</html>