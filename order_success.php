
<?php

// Get order info from query string
$order_id = $_GET['order_id'] ?? null;
$order_number = $_GET['order_number'] ?? null;

if (!$order_id || !$order_number) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .success-container {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 64px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 1.6rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .order-info {
            margin-top: 20px;
            font-size: 1.1rem;
            color: #666;
        }
        .back-home {
            display: inline-block;
            margin-top: 30px;
            background: #557c3e;
            color: #fff;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-home:hover {
            background: #466832;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="success-container">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <div class="success-message">Cảm ơn bạn đã đặt hàng!</div>
        <div class="order-info">
            Mã đơn hàng của bạn là <strong><?php echo htmlspecialchars($order_number); ?></strong><br>
            Chúng tôi sẽ sớm liên hệ để xác nhận đơn hàng.
        </div>
        <a href="index.php" class="back-home"><i class="fas fa-home"></i> Quay lại trang chủ</a>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>