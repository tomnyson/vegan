<?php

require_once 'helpers/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt hàng']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

try {
    $pdo = db_connect();
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'];
    
    // Get form data
    $fullname = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $payment_method = $_POST['payment'] ?? 'cod';
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $shipping_fee = floatval($_POST['shipping_fee'] ?? 0);
    $cart_items = json_decode($_POST['cart_items'] ?? '[]', true);

    // Validate required fields
    if (empty($fullname) || empty($phone) || empty($address) || empty($cart_items)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
    }

    if ($total_amount <= 0) {
        throw new Exception('Tổng tiền đơn hàng không hợp lệ');
    };

    // Generate unique order number
    $order_number = 'ORD' . date('YmdHis') . sprintf('%03d', rand(1, 999));

    // Insert order
    $order_sql = "INSERT INTO orders (
        user_id, order_number, customer_name, customer_phone, customer_email,
        delivery_address, notes, payment_method, subtotal, shipping_fee,
        total_amount, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $order_stmt = $pdo->prepare($order_sql);
    $order_result = $order_stmt->execute([
        $user_id,
        $order_number,
        $fullname,
        $phone,
        $email,
        $address,
        $notes,
        $payment_method,
        $subtotal,
        $shipping_fee,
        $total_amount
    ]);

    if (!$order_result) {
        throw new Exception('Không thể tạo đơn hàng');
    }

    $order_id = $pdo->lastInsertId();

    // Function to calculate item total with toppings (same as in checkout.php)
    function calculateItemTotalWithToppings($basePrice, $quantity, $options) {
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

    // Insert order items
    $item_sql = "INSERT INTO order_items (
        order_id, product_id, product_name, product_price, quantity, options, item_total
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $item_stmt = $pdo->prepare($item_sql);

    foreach ($cart_items as $item) {
        $item_total = calculateItemTotalWithToppings(
            $item['product_price'],
            $item['quantity'],
            $item['options']
        );

        $item_result = $item_stmt->execute([
            $order_id,
            $item['product_id'],
            $item['product_name'],
            $item['product_price'],
            $item['quantity'],
            $item['options'],
            $item_total
        ]);

        if (!$item_result) {
            throw new Exception('Không thể thêm sản phẩm vào đơn hàng');
        }
    }

    // Clear user's cart after successful order
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = $pdo->prepare($clear_cart_sql);
    $clear_cart_stmt->execute([$user_id]);

    // Commit transaction
    $pdo->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Đặt hàng thành công',
        'order_id' => $order_id,
        'order_number' => $order_number
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    // Rollback transaction on database error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()
    ]);
}
?>