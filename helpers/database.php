<?php
// Show all PHP errors
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function db_connect() {
    // Fallback to local MySQL
    $host = 'localhost';
    $dbname = 'vegan';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Kết nối thất bại: " . $e->getMessage());
    }
}

// Thực thi SELECT trả về nhiều hàng
function db_query_all($sql, $params = []) {
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Thực thi SELECT trả về 1 hàng
function db_query_one($sql, $params = []) {
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Thực thi INSERT, UPDATE, DELETE
function db_execute($sql, $params = []) {
    $conn = db_connect();
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

// Trả về giá trị đầu tiên (dạng scalar) từ câu query, ví dụ COUNT(*)
function db_query_value($sql, $params = []) {
    $pdo = db_connect();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    $stmt = null;
    return $value;
}