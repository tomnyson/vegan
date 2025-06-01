<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_food";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Join để lấy thông tin món ăn từ bảng food
$sql = "SELECT c.Id AS CartId, f.Id AS FoodId, f.Title, f.Price, f.ImageUrl 
        FROM cart c 
        JOIN food f ON c.FoodId = f.Id";

$result = $conn->query($sql);
$cartItems = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($cartItems);

$conn->close();
?>
