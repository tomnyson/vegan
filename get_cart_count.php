<?php
session_start();
require_once 'helpers/database.php';

// Get total items in cart for current user
if (isset($_SESSION['user_id'])) {
    $count = db_query_value("SELECT SUM(quantity) FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
    $count = $count ?? 0;
} else {
    $count = 0;
}

echo json_encode(["count" => (int)$count]);
?>