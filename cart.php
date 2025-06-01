<?php
ob_start();
session_start();

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
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Nhà Hàng Chay</title>
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

        .mobile-menu {
            display: none;
        }

        /* Cart Container */
        .cart-container {
            max-width: 1000px;
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

        /* Cart Items */
        .cart-item {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cart-item-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details strong {
            display: block;
            color: var(--text-dark);
            font-size: 1rem;
            margin-bottom: 3px;
        }

        .cart-item-details p {
            color: var(--accent-color);
            font-weight: bold;
            font-size: 0.95rem;
            margin: 0;
        }

        .cart-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .options-toggle {
            background: var(--bg-accent);
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .options-toggle:hover {
            background: var(--primary-color);
            color: white;
        }

        .options-toggle i {
            transition: transform 0.3s ease;
        }

        .options-toggle.expanded i {
            transform: rotate(180deg);
        }

        .delete-button {
            background: var(--highlight);
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-button:hover {
            background: #c0392b;
            transform: scale(1.1);
        }

        /* Empty Cart */
        .emptyContainer {
            text-align: center;
            padding: 60px 20px;
        }

        .emptyContainer img {
            max-width: 200px;
            opacity: 0.7;
            margin-bottom: 30px;
        }

        .btn-buyEmpty {
            background: var(--primary-color);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 30px;
        }

        .btn-buyEmpty:hover {
            background: #466832;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Cart Total and Checkout */
        .cart-summary {
            background: var(--bg-accent);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            text-align: right;
        }

        .total-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .checkout-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .checkout-btn:hover {
            background: #c0981c;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Toast Notifications */
        #toast {
            position: fixed;
            top: 32px;
            right: 32px;
            z-index: 999999;
        }

        .toast {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 2px;
            padding: 20px 0;
            min-width: 400px;
            max-width: 450px;
            border-left: 4px solid;
            box-shadow: 0 5px 8px rgba(0, 0, 0, 0.08);
            transition: all linear 0.3s;
        }

        .toast--success {
            border-color: #47d864;
        }

        .toast--error {
            border-color: #ff623d;
        }

        .toast__icon {
            font-size: 24px;
            margin-left: 16px;
            margin-right: 8px;
        }

        .toast--success .toast__icon {
            color: #47d864;
        }

        .toast--error .toast__icon {
            color: #ff623d;
        }

        .toast__body {
            flex-grow: 1;
        }

        .toast__title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .toast__msg {
            font-size: 14px;
            color: #888;
            margin-top: 6px;
            line-height: 1.5;
        }

        .toast__close {
            font-size: 20px;
            color: rgba(0, 0, 0, 0.3);
            cursor: pointer;
            padding: 0 16px;
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

            .cart-container {
                margin: 20px 10px;
                padding: 20px 15px;
            }

            .cart-item-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .cart-item img {
                width: 60px;
                height: 60px;
            }

            .footer-section {
                flex: 100%;
            }

            .toast {
                min-width: 300px;
                max-width: 350px;
            }
        }

        @media (max-width: 600px) {
            .section-title {
                font-size: 1.8rem;
            }

            #toast {
                right: 16px;
                left: 16px;
            }

            .toast {
                min-width: auto;
                max-width: none;
            }
        }

        /* Animations */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
            }
        }

        .cart-subtotal {
            font-size: 1rem;
            color: #2e7d32;
            font-weight: 600;
            margin-top: 6px;
        }

        /* Cart item options styles */
        .cart-options {
            background: #fafaf2;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 12px;
            border: 1px dashed #ccc;
        }

        .option-group {
            margin-bottom: 6px;
        }

        .option-group-label {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .option-items {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .option-item {
            background: #eef3e5;
            color: #333;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 0.85rem;
            border: 1px solid #ccc;
        }

        .option-item.with-price {
            background: #d4af37;
            color: #fff;
            font-weight: 600;
        }

        .cart-note {
            margin-top: 8px;
            padding: 6px 10px;
            background: #fff9e6;
            border-left: 3px solid var(--accent-color);
            border-radius: 0 4px 4px 0;
            font-size: 0.85rem;
            color: #7a6c00;
        }

        .cart-note-label {
            font-weight: 600;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <?php
    require_once 'helpers/database.php';
    include 'includes/header.php';
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    ?>
    <?php
    $query_cart = "SELECT c.id, c.product_id, c.product_name, c.product_price, c.quantity, c.options, c.created_at, p.image
                  FROM cart c
                  INNER JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.created_at DESC";

    $carts = db_query_all($query_cart, [':user_id' => $_SESSION['user_id'] ?? 0]);
    ?>
    <div class="cart-container">
        <h2 class="section-title">Giỏ Hàng Của Bạn</h2>
        <div id="listProduct">
            <?php if (empty($carts)): ?>
                <div class="emptyContainer">
                    <i class="fas fa-shopping-cart" style="font-size: 6rem; color: #ccc; margin-bottom: 20px;"></i>
                    <p>Giỏ hàng của bạn đang trống.</p>
                    <a href="index.php" class="btn-buyEmpty">Tiếp tục mua sắm</a>
                </div>
            <?php else: ?>
                <?php
                $total = 0;
                foreach ($carts as $item):
                    // Calculate subtotal including toppings
                    $subtotal = calculateItemTotalWithToppings($item['product_price'], $item['quantity'], $item['options']);
                    $total += $subtotal;

                    // Calculate base price for display
                    $baseSubtotal = $item['product_price'] * $item['quantity'];

                    // Calculate topping price
                    $toppingPrice = $subtotal - $baseSubtotal;
                ?>
                    <div class="cart-item">
                        <div class="cart-item-content">
                            <?php

                            $image_src = '';
                            if (!empty($product['image']) && file_exists($product['image'])) {
                                $image_src = htmlspecialchars($product['image']);
                            } else {
                                $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
                            } ?>
                            <img src="<?= $image_src ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                            <div class="cart-item-details">
                                <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                <p><?= number_format($item['product_price'], 0, ',', '.') ?> VNĐ x <?= $item['quantity'] ?></p>
                                <?php if ($toppingPrice > 0): ?>
                                    <p style="font-size: 0.85rem; color: #666; margin-top: 2px;">
                                        + Topping: <?= number_format($toppingPrice, 0, ',', '.') ?> VNĐ
                                    </p>
                                <?php endif; ?>
                                <p class="cart-subtotal">Thành tiền: <?= number_format($subtotal, 0, ',', '.') ?> VNĐ</p>
                            </div>
                            <div class="cart-actions">
                                <?php if (!empty($item['options'])): ?>
                                    <button class="options-toggle" onclick="toggleOptions(<?= $item['id'] ?>)">
                                        <i class="fas fa-chevron-down"></i>
                                        Chi tiết
                                    </button>
                                <?php endif; ?>
                                <form method="post" action="remove_from_cart.php" style="margin: 0;">
                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="delete-button"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <?php if (!empty($item['options'])): ?>
                            <div class="cart-options" id="options-<?= $item['id'] ?>" style="display: none;">
                                <?php
                                $options = json_decode($item['options'], true);
                                if ($options && isset($options['selectedOptions'])):
                                    $selectedOptions = $options['selectedOptions'];
                                ?>
                                    <?php foreach ($selectedOptions as $groupId => $groupData): ?>
                                        <div class="option-group">
                                            <?php if (isset($groupData['type']) && $groupData['type'] === 'multiple' && isset($groupData['items'])): ?>
                                                <div class="option-group-label">Topping/Món phụ:</div>
                                                <div class="option-items">
                                                    <?php foreach ($groupData['items'] as $item_option): ?>
                                                        <div class="option-item with-price">
                                                            <i class="fas fa-plus-circle"></i>
                                                            <?= htmlspecialchars($item_option['label']) ?>
                                                            <?php if (isset($item_option['price']) && $item_option['price'] > 0): ?>
                                                                (+<?= number_format($item_option['price'], 0, ',', '.') ?>đ)
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php elseif (isset($groupData['type']) && $groupData['type'] === 'single' && isset($groupData['label'])): ?>
                                                <div class="option-group-label">
                                                    <?php
                                                    if ($groupId == '1') echo 'Kích thước';
                                                    elseif ($groupId == '2') echo 'Hương vị';
                                                    else echo 'Tùy chọn';
                                                    ?>:
                                                </div>
                                                <div class="option-items">
                                                    <div class="option-item">
                                                        <?= htmlspecialchars($groupData['label']) ?>
                                                        <?php if (isset($groupData['price']) && $groupData['price'] > 0): ?>
                                                            (+<?= number_format($groupData['price'], 0, ',', '.') ?>đ)
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (isset($options['note']) && !empty($options['note'])): ?>
                                    <div class="cart-note">
                                        <span class="cart-note-label"><i class="fas fa-sticky-note"></i> Ghi chú:</span>
                                        <?= htmlspecialchars($options['note']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <script>
                    document.getElementById("cart-summary").style.display = "block";
                    document.getElementById("total-amount").textContent = "<?= number_format($total, 0, ',', '.') ?> VNĐ";
                </script>
            <?php endif; ?>
        </div>
        <?php if (!empty($carts)): ?>
            <div id="cart-summary" class="cart-summary">
                <div class="total-price">
                    Tổng cộng: <span id="total-amount"><?= number_format($total, 0, ',', '.') ?> VNĐ</span>
                </div>
                <a href="checkout.php" class="checkout-btn">
                    <i class="fas fa-credit-card"></i> Thanh toán
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="./assets/js/script.js"></script>

    <script>
        function toggleOptions(itemId) {
            const optionsDiv = document.getElementById('options-' + itemId);
            const toggleBtn = document.querySelector(`[onclick="toggleOptions(${itemId})"]`);

            if (optionsDiv.style.display === 'none' || optionsDiv.style.display === '') {
                optionsDiv.style.display = 'block';
                toggleBtn.classList.add('expanded');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Ẩn chi tiết';
            } else {
                optionsDiv.style.display = 'none';
                toggleBtn.classList.remove('expanded');
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Chi tiết';
            }
        }
    </script>
</body>
<?php ob_end_flush(); ?>

</html>