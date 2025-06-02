<?php
require_once 'helpers/database.php';

$pdo = db_connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Vui lòng đăng nhập để thêm vào giỏ hàng"]);
    exit;
}

$userId = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);

$productId = $data['productId'] ?? null;
$productName = $data['productName'] ?? '';
$productPrice = $data['productPrice'] ?? 0;
$quantity = $data['quantity'] ?? 1;
$selectedOptions = $data['selectedOptions'] ?? [];
$note = $data['note'] ?? '';

if ($productId !== null) {
    $optionsJson = json_encode([
        'selectedOptions' => $selectedOptions,
        'note' => $note
    ]);

    $stmt = $pdo->prepare("SELECT Id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id AND options = :options");
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':options' => $optionsJson
    ]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $newQuantity = $row['quantity'] + $quantity;

        $update = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE Id = :id");
        if ($update->execute([':quantity' => $newQuantity, ':id' => $row['Id']])) {
            echo json_encode(["success" => true, "message" => "Đã cập nhật số lượng trong giỏ hàng"]);
        } else {
            echo json_encode(["success" => false, "error" => $update->errorInfo()[2]]);
        }
    } else {
        $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity, options, created_at) VALUES (:user_id, :product_id, :product_name, :product_price, :quantity, :options, NOW())");
        $success = $insert->execute([
            ':user_id' => $userId,
            ':product_id' => $productId,
            ':product_name' => $productName,
            ':product_price' => $productPrice,
            ':quantity' => $quantity,
            ':options' => $optionsJson
        ]);

        if ($success) {
            echo json_encode(["success" => true, "message" => "Đã thêm vào giỏ hàng"]);
        } else {
            echo json_encode(["success" => false, "error" => $insert->errorInfo()[2]]);
        }
    }
} else {
    echo json_encode(["success" => false, "error" => "Thiếu thông tin sản phẩm"]);
}

exit;
?>