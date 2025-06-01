<?php

include "./helpers/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartId = $_POST['cart_id'] ?? null;

    if ($cartId !== null) {
        $result = db_execute("DELETE FROM cart WHERE Id = ?", [$cartId]);
        if ($result) {
            header("Location: cart.php");
            exit;
        } else {
            $_SESSION['error'] = "Xóa giỏ hàng thất bại";
            header("Location: cart.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Thiếu cart_id";
        header("Location: cart.php");
        exit;
    }
} else {
    header("Location: cart.php");
    exit;
}
