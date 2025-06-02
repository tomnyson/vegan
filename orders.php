<?php
require_once 'helpers/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$pdo = db_connect();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử đơn hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<link rel="stylesheet" href="./assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    .order-list {
        margin-top: 20px;
    }

    .order-item {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .order-summary th,
    .order-summary td {
        border-bottom: 1px solid #eee;
    }

    .order-summary th {
        background-color: #f9f9f9;
        font-weight: 600;
    }

    .order-summary td {
        font-size: 14px;
    }

    button {
        background-color: #557c3e;
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #44662e;
    }

    table {
        margin-top: 10px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
</style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2 class="section-title">Lịch sử đơn hàng</h2>

        <?php if (count($orders) === 0): ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php else: ?>
            <div class="order-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <table class="order-summary" style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
                            <thead>
                                <tr style="background-color:#f2f2f2;">
                                    <th style="text-align:left; padding:8px;">Mã đơn hàng</th>
                                    <th style="text-align:left; padding:8px;">Ngày đặt</th>
                                    <th style="text-align:left; padding:8px;">Giá sản phẩm</th>
                                    <th style="text-align:left; padding:8px;">Tổng tiền</th>
                                    <th style="text-align:left; padding:8px;">Phí ship</th>
                                    <th style="text-align:left; padding:8px;">Ghi chú</th>
                                    <th style="text-align:left; padding:8px;">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px;"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td style="padding:8px;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td style="padding:8px;"><?php echo isset($order['subtotal']) ? number_format((float)$order['subtotal'], 0, ',', '.') . ' VNĐ' : 'Không có'; ?></td>
                                    <td style="padding:8px;"><?php echo isset($order['total_amount']) ? number_format((float)$order['total_amount'], 0, ',', '.') . ' VNĐ' : 'Không có'; ?></td>
                                    <td style="padding:8px;"><?php 
                                        if (isset($order['shipping_fee'])) {
                                            echo $order['shipping_fee'] > 0 ? number_format((float)$order['shipping_fee'], 0, ',', '.') . ' VNĐ' : 'Miễn phí';
                                        } else {
                                            echo 'Không có';
                                        }
                                    ?></td>
                                    <td style="padding:8px;"><?php echo isset($order['note']) ? htmlspecialchars($order['note']) : 'Không có'; ?></td>
                                    <td style="padding:8px;">
                                        <button onclick="toggleDetails('details-<?php echo $order['id']; ?>')">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div id="details-<?php echo $order['id']; ?>" style="display:none; margin-left: 20px;">
                            <?php
                            $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                            $stmtItems->execute([$order['id']]);
                            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <table style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background-color:#e8e8e8;">
                                        <th style="text-align:left; padding:6px;">Mã SP</th>
                                        <th style="text-align:left; padding:6px;">Tên SP</th>
                                        <th style="text-align:left; padding:6px;">Tùy chọn</th>
                                        <th style="text-align:left; padding:6px;">Số lượng</th>
                                        <th style="text-align:left; padding:6px;">Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <?php
                                            try {
                                                $options = json_decode($item['options'], true, 512, JSON_THROW_ON_ERROR);
                                            } catch (Exception $e) {
                                                $options = null;
                                            }
                                            ?>
                                            <td style="padding:6px;"><?php echo htmlspecialchars($item['product_id']); ?></td>
                                            <td style="padding:6px;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <?php if (!empty($options) && isset($options['selectedOptions'])): ?>
                                                <td style="padding:6px;">
                                                    <?php
                                                    $selectedOptions = $options['selectedOptions'];
                                                    foreach ($selectedOptions as $groupId => $groupData) {
                                                        if (isset($groupData['type']) && $groupData['type'] === 'single' && isset($groupData['label'])) {
                                                            // Single choice option
                                                            echo '<strong>';
                                                            if ($groupId == '1') echo 'Kích thước';
                                                            elseif ($groupId == '2') echo 'Hương vị';
                                                            else echo 'Tùy chọn';
                                                            echo ':</strong> ' . htmlspecialchars($groupData['label']);
                                                            if (isset($groupData['price']) && $groupData['price'] > 0) {
                                                                echo ' (+' . number_format($groupData['price'], 0, ',', '.') . 'đ)';
                                                            }
                                                            echo '<br>';
                                                        } elseif (isset($groupData['type']) && $groupData['type'] === 'multiple' && isset($groupData['items'])) {
                                                            // Multiple choice options (toppings)
                                                            echo '<strong>Topping/Món phụ:</strong> ';
                                                            $labels = [];
                                                            foreach ($groupData['items'] as $item_option) {
                                                                $label = htmlspecialchars($item_option['label']);
                                                                if (isset($item_option['price']) && $item_option['price'] > 0) {
                                                                    $label .= ' (+' . number_format($item_option['price'], 0, ',', '.') . 'đ)';
                                                                }
                                                                $labels[] = $label;
                                                            }
                                                            echo implode(', ', $labels) . '<br>';
                                                        }
                                                    }

                                                    // Display note if exists
                                                    if (isset($options['note']) && !empty($options['note'])) {
                                                        echo '<strong>Ghi chú:</strong> ' . htmlspecialchars($options['note']) . '<br>';
                                                    }
                                                    ?>
                                                </td>
                                            <?php else: ?>
                                                <td style="padding:6px;">Không có</td>
                                            <?php endif; ?>
                                            <td style="padding:6px;"><?php echo (int)$item['quantity']; ?></td>
                                            <td style="padding:6px;"><?php
                                                                        echo isset($item['product_price']) ? number_format((float)$item['product_price'], 0, ',', '.') . ' VNĐ' : 'Không có'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            if (el.style.display === "none") {
                el.style.display = "block";
            } else {
                el.style.display = "none";
            }
        }
    </script>
</body>

</html>