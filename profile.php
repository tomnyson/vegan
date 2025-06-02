<?php
require_once 'helpers/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Tên không được để trống";
        }
        if (empty($email)) {
            $errors[] = "Email không được để trống";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ";
        }
        if (empty($phone)) {
            $errors[] = "Số điện thoại không được để trống";
        }
        
        // Check if email is already used by another user
        if (empty($errors)) {
            $existing_user = db_query_one("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user_id]);
            if ($existing_user) {
                $errors[] = "Email này đã được sử dụng bởi tài khoản khác";
            }
        }
        
        if (empty($errors)) {
            try {
                $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
                $result = db_execute($sql, [$name, $email, $phone, $user_id]);
                
                if ($result) {
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $name;
                    $success_message = "Cập nhật thông tin thành công!";
                } else {
                    $error_message = "Có lỗi xảy ra khi cập nhật thông tin";
                }
            } catch (Exception $e) {
                $error_message = "Lỗi database: " . $e->getMessage();
            }
        } else {
            $error_message = implode(', ', $errors);
        }
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($current_password)) {
            $errors[] = "Vui lòng nhập mật khẩu hiện tại";
        }
        if (empty($new_password)) {
            $errors[] = "Vui lòng nhập mật khẩu mới";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Mật khẩu xác nhận không khớp";
        }
        
        if (empty($errors)) {
            // Verify current password
            $user = db_query_one("SELECT password FROM users WHERE id = ?", [$user_id]);
            if (!$user || !password_verify($current_password, $user['password'])) {
                $error_message = "Mật khẩu hiện tại không đúng";
            } else {
                try {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET password = ? WHERE id = ?";
                    $result = db_execute($sql, [$hashed_password, $user_id]);
                    
                    if ($result) {
                        $success_message = "Đổi mật khẩu thành công!";
                    } else {
                        $error_message = "Có lỗi xảy ra khi đổi mật khẩu";
                    }
                } catch (Exception $e) {
                    $error_message = "Lỗi database: " . $e->getMessage();
                }
            }
        } else {
            $error_message = implode(', ', $errors);
        }
    }
}

// Get user information
try {
    $user = db_query_one("SELECT * FROM users WHERE id = ?", [$user_id]);
    if (!$user) {
        header('Location: login.php');
        exit();
    }
} catch (Exception $e) {
    $error_message = "Không thể lấy thông tin người dùng";
}

// Get user's order history
try {
    $orders = db_query_all("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$user_id]);
} catch (Exception $e) {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân - Nhà Hàng Chay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .profile-wrapper {
            background: #f8f7f3;
            padding: 40px 20px;
            min-height: calc(100vh - 200px);
        }

        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-header {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #557c3e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: white;
        }

        .profile-name {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .profile-email {
            color: #666;
            font-size: 1.1rem;
        }

        .profile-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .profile-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .section-title {
            color: #557c3e;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
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
            padding: 12px 15px;
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

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .btn-primary {
            background: #557c3e;
            color: white;
        }

        .btn-primary:hover {
            background: #4a6b35;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .order-item {
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-id {
            font-weight: bold;
            color: #557c3e;
        }

        .order-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .tab {
            padding: 12px 20px;
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .tab.active {
            color: #557c3e;
            border-bottom-color: #557c3e;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .profile-wrapper {
                padding: 20px 15px;
            }
            
            .profile-section {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="profile-wrapper">
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name">
                    <?php echo htmlspecialchars($user['name']); ?>
                </div>
                <div class="profile-email">
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($success_message): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Left Column - Profile Information -->
                <div class="profile-section">
                    <div class="tabs">
                        <button class="tab active" onclick="showTab('profile-info')">
                            <i class="fas fa-user"></i> Thông tin cá nhân
                        </button>
                        <button class="tab" onclick="showTab('change-password')">
                            <i class="fas fa-lock"></i> Đổi mật khẩu
                        </button>
                    </div>

                    <!-- Profile Information Tab -->
                    <div id="profile-info" class="tab-content active">
                        <h3 class="section-title">
                            <i class="fas fa-user-edit"></i>
                            Cập nhật thông tin
                        </h3>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i> Họ và Tên
                                </label>
                                <input type="text" id="name" name="name" required
                                    value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" id="email" name="email" required
                                    value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Số điện thoại
                                </label>
                                <input type="tel" id="phone" name="phone" required
                                    value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật thông tin
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="change-password" class="tab-content">
                        <h3 class="section-title">
                            <i class="fas fa-key"></i>
                            Đổi mật khẩu
                        </h3>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="current_password">
                                    <i class="fas fa-lock"></i> Mật khẩu hiện tại
                                </label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label for="new_password">
                                    <i class="fas fa-key"></i> Mật khẩu mới
                                </label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">
                                    <i class="fas fa-key"></i> Xác nhận mật khẩu mới
                                </label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column - Order History -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-history"></i>
                        Lịch sử đơn hàng gần đây
                    </h3>

                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <span class="order-id">#<?php echo htmlspecialchars($order['id']); ?></span>
                                    <span class="order-status status-<?php echo htmlspecialchars($order['status']); ?>">
                                        <?php 
                                        $status_labels = [
                                            'pending' => 'Đang xử lý',
                                            'confirmed' => 'Đã xác nhận',
                                            'preparing' => 'Đang chuẩn bị',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $status_labels[$order['status']] ?? 'Không xác định';
                                        ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount']); ?>₫</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="orders.php" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Xem tất cả đơn hàng
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px 20px; color: #666;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>Bạn chưa có đơn hàng nào</p>
                            <a href="products.php" class="btn btn-primary" style="margin-top: 10px;">
                                <i class="fas fa-utensils"></i> Đặt món ngay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Profile form validation
            const profileForm = document.querySelector('form[name="update_profile"]');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const name = document.getElementById('name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const phone = document.getElementById('phone').value.trim();
                    
                    if (!name || !email || !phone) {
                        e.preventDefault();
                        alert('Vui lòng điền đầy đủ thông tin');
                        return false;
                    }
                    
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        alert('Email không hợp lệ');
                        return false;
                    }
                });
            }
            
            // Password form validation
            const passwordForm = document.querySelector('form[name="change_password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('Mật khẩu xác nhận không khớp');
                        return false;
                    }
                    
                    if (newPassword.length < 6) {
                        e.preventDefault();
                        alert('Mật khẩu mới phải có ít nhất 6 ký tự');
                        return false;
                    }
                });
            }
        });
    </script>
    <script src="./assets/js/script.js"></script>
</body>
</html>