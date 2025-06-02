<?php
require_once 'helpers/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Function to calculate total price including toppings
function calculateItemTotalWithToppings($basePrice, $quantity, $options)
{
    $itemTotal = $basePrice * $quantity;

    if (!empty($options)) {
        $optionsData = json_decode($options, true);
        if ($optionsData && isset($optionsData['selectedOptions'])) {
            foreach ($optionsData['selectedOptions'] as $groupData) {
                if (isset($groupData['type']) && $groupData['type'] === 'multiple' && isset($groupData['items'])) {
                    // Multiple choice options (toppings)
                    foreach ($groupData['items'] as $item) {
                        if (isset($item['price'])) {
                            $itemTotal += ($item['price'] * $quantity);
                        }
                    }
                } elseif (isset($groupData['type']) && $groupData['type'] === 'single' && isset($groupData['price'])) {
                    // Single choice options with price
                    $itemTotal += ($groupData['price'] * $quantity);
                }
            }
        }
    }

    return $itemTotal;
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_query = "SELECT name, email, phone FROM users WHERE id = ? AND status = 'active'";
$user_info = db_query_one($user_query, [$user_id]);

// Get cart items
$cart_query = "SELECT c.id, c.product_id, c.product_name, c.product_price, c.quantity, c.options, c.created_at, p.image
              FROM cart c
              INNER JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?
              ORDER BY c.created_at DESC";

$cart_items = db_query_all($cart_query, [$user_id]);

// Calculate totals
$subtotal = 0;
$shipping_fee = 0;
$discount = 0;

foreach ($cart_items as $item) {
    $itemTotal = calculateItemTotalWithToppings($item['product_price'], $item['quantity'], $item['options']);
    $subtotal += $itemTotal;
}

// Calculate shipping fee (free shipping over 500,000 VND)
if ($subtotal > 0 && $subtotal < 500000) {
    $shipping_fee = 30000; // 30,000 VND shipping fee
}

$total = $subtotal + $shipping_fee - $discount;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Nhà Hàng Chay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Base styles matching index.html */
        :root {
            --primary-color: #557c3e;
            --accent-color: #d4af37;
            --text-dark: #333;
            --text-light: #fff;
            --bg-light: #f8f7f2;
            --bg-accent: #f2efdf;
            --highlight: #e74c3c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header and Navigation - matching index.html */
        header {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container i {
            font-size: 1.8rem;
            margin-right: 10px;
            color: var(--accent-color);
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav a {
            color: var(--text-light);
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            position: relative;
            padding: 5px 0;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        nav a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--accent-color);
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }

        nav a:hover:after {
            width: 100%;
        }

        nav a.active {
            color: var(--accent-color);
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-light);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Main Container */
        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 40px 30px;
            min-height: 400px;
        }

        .section-title {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 40px;
            font-size: 2.2rem;
            position: relative;
            font-weight: 700;
        }

        .section-title:after {
            content: "";
            position: absolute;
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .breadcrumb a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: bold;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            margin: 0 10px;
            color: #aaa;
        }

        .breadcrumb .current {
            color: var(--primary-color);
            font-weight: bold;
        }

        /* Form Styles */
        .checkout-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }

        .form-section {
            background: #fff;
        }

        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 1.3rem;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--bg-accent);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-dark);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            height: 100px;
        }

        /* Order Summary */
        .order-summary {
            background: var(--bg-accent);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .order-summary h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .order-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
            color: var(--primary-color);
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid var(--accent-color);
        }

        .item-name {
            font-weight: bold;
        }

        .item-quantity {
            color: #666;
            font-size: 0.9rem;
        }

        .item-price {
            color: var(--accent-color);
            font-weight: bold;
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            gap: 15px;
            margin-top: 10px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--accent-color);
            background: rgba(212, 175, 55, 0.05);
        }

        .payment-option input[type="radio"] {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .payment-option label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 0;
        }

        .payment-option i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: var(--accent-color);
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: space-between;
            margin-top: 40px;
            grid-column: 1 / -1;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background: #c0981c;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Loading State */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .loading {
            position: relative;
            color: transparent;
        }

        .loading:after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s ease infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Footer matching index.html */
        footer {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 50px 0 20px;
            text-align: center;
            margin-top: 80px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 0 20px;
        }

        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 30px;
            text-align: left;
        }

        .footer-section h3 {
            color: var(--accent-color);
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .footer-section p {
            margin-bottom: 10px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-icons a {
            color: var(--text-light);
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--accent-color);
        }

        .footer-bottom {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            nav {
                position: absolute;
                top: 60px;
                left: 0;
                background: var(--primary-color);
                width: 100%;
                flex-direction: column;
                padding: 20px 0;
                transform: translateY(-200%);
                transition: transform 0.3s ease;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }

            nav.active {
                transform: translateY(0);
            }

            nav a {
                margin: 10px 0;
            }

            .checkout-container {
                margin: 20px 10px;
                padding: 20px 15px;
            }

            .checkout-form {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .form-actions {
                flex-direction: column;
            }

            .footer-section {
                flex: 100%;
            }
        }

        @media (max-width: 600px) {
            .section-title {
                font-size: 1.8rem;
            }

            .breadcrumb {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="checkout-container">
        <h2 class="section-title">Thanh Toán</h2>

        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
            <span>></span>
            <a href="Cart.php">Giỏ hàng</a>
            <span>></span>
            <span class="current">Thanh toán</span>
        </div>

        <?php if (empty($cart_items)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p style="color: #666; margin-bottom: 30px;">Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán</p>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-basket"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php else: ?>
            <form id="checkout-form" class="checkout-form">
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Thông tin giao hàng</h3>

                    <div class="form-group">
                        <label for="fullname">Họ và tên *</label>
                        <input type="text" id="fullname" name="name" required
                            value="<?= htmlspecialchars($user_info['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại *</label>
                        <input type="tel" id="phone" name="phone" required
                            value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?= htmlspecialchars($user_info['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Địa chỉ giao hàng *</label>
                        <textarea id="address" name="address" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes">Ghi chú đơn hàng</label>
                        <textarea id="notes" name="notes" placeholder="Ghi chú thêm về đơn hàng, ví dụ: thời gian giao hàng mong muốn"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="order-summary" id="order-summary">
                        <h3><i class="fas fa-receipt"></i> Tóm tắt đơn hàng</h3>
                        <div id="order-items">
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                $itemTotal = calculateItemTotalWithToppings($item['product_price'], $item['quantity'], $item['options']);
                                $baseSubtotal = $item['product_price'] * $item['quantity'];
                                $toppingPrice = $itemTotal - $baseSubtotal;
                                ?>
                                <div class="order-item">
                                    <div class="item-details">
                                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="item-quantity">Số lượng: <?= $item['quantity'] ?></div>
                                        <?php if ($toppingPrice > 0): ?>
                                            <div class="item-toppings">
                                                + Topping: <?= number_format($toppingPrice, 0, ',', '.') ?>đ
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($item['options'])): ?>
                                            <?php
                                            $options = json_decode($item['options'], true);
                                            if ($options && isset($options['selectedOptions'])):
                                            ?>
                                                <div class="item-options">
                                                    <?php foreach ($options['selectedOptions'] as $groupId => $groupData): ?>
                                                        <?php if (isset($groupData['type']) && $groupData['type'] === 'multiple' && isset($groupData['items'])): ?>
                                                            <?php foreach ($groupData['items'] as $option): ?>
                                                                <span class="option-tag">+ <?= htmlspecialchars($option['label']) ?></span>
                                                            <?php endforeach; ?>
                                                        <?php elseif (isset($groupData['label'])): ?>
                                                            <span class="option-tag"><?= htmlspecialchars($groupData['label']) ?></span>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-price"><?= number_format($itemTotal, 0, ',', '.') ?>đ</div>
                                </div>
                            <?php endforeach; ?>

                            <div class="order-subtotal">
                                <div class="order-item">
                                    <span>Tạm tính:</span>
                                    <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                </div>
                                <div class="order-item">
                                    <span>Phí vận chuyển:</span>
                                    <span><?= $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . 'đ' : 'Miễn phí' ?></span>
                                </div>
                                <?php if ($discount > 0): ?>
                                    <div class="order-item">
                                        <span>Giảm giá:</span>
                                        <span>-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="order-item order-total">
                                <span>Tổng cộng:</span>
                                <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>

                            <?php if ($subtotal < 500000): ?>
                                <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px; font-size: 0.9rem; color: #856404;">
                                    <i class="fas fa-info-circle"></i>
                                    Mua thêm <?= number_format(500000 - $subtotal, 0, ',', '.') ?>đ để được miễn phí vận chuyển
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" id="cod" name="payment" value="cod" checked>
                            <label for="cod">
                                <i class="fas fa-money-bill-wave"></i>
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="bank" name="payment" value="bank">
                            <label for="bank">
                                <i class="fas fa-university"></i>
                                Chuyển khoản ngân hàng
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="wallet" name="payment" value="wallet">
                            <label for="wallet">
                                <i class="fas fa-mobile-alt"></i>
                                Ví điện tử (Momo, ZaloPay)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="Cart.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-check"></i> Xác nhận đặt hàng
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Auto-fill user information if available
        document.addEventListener('DOMContentLoaded', function() {
            // Add cart data to form for submission
            const form = document.getElementById('checkout-form');
            if (form) {
                // Add hidden input with cart total
                const totalInput = document.createElement('input');
                totalInput.type = 'hidden';
                totalInput.name = 'total_amount';
                totalInput.value = '<?= $total ?>';
                form.appendChild(totalInput);

                // Add hidden input with subtotal
                const subtotalInput = document.createElement('input');
                subtotalInput.type = 'hidden';
                subtotalInput.name = 'subtotal';
                subtotalInput.value = '<?= $subtotal ?>';
                form.appendChild(subtotalInput);

                // Add hidden input with shipping fee
                const shippingInput = document.createElement('input');
                shippingInput.type = 'hidden';
                shippingInput.name = 'shipping_fee';
                shippingInput.value = '<?= $shipping_fee ?>';
                form.appendChild(shippingInput);
            }

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');

                // Collect form data
                const formData = new FormData(this);

                // Add cart items data
                const cartItems = <?= json_encode($cart_items) ?>;
                formData.append('cart_items', JSON.stringify(cartItems));

                // Submit order
                fetch('process_order.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect to success page
                            window.location.href = 'order_success.php?order_id=' + data.order_id + '&order_number=' + data.order_number;
                        } else {
                            alert('Có lỗi xảy ra: ' + data.message);
                            // Restore button
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('loading');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xử lý đơn hàng');
                        // Restore button
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('loading');
                    });
            });
        });
    </script>
</body>

</html>