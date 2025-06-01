<?php
session_start();
require_once 'helpers/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    // Check required fields
    if (empty($name)) {
        $errors[] = "Tên không được để trống";
    }
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($phone)) {
        $errors[] = "Số điện thoại không được để trống";
    } 
    // elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    //     $errors[] = "Số điện thoại phải có 10-11 chữ số";
    // }
    
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    }
    //  elseif (strlen($password) < 8) {
    //     $errors[] = "Mật khẩu phải có ít nhất 8 ký tự";
    // } elseif (!preg_match('/[A-Z]/', $password)) {
    //     $errors[] = "Mật khẩu phải chứa ít nhất 1 chữ hoa";
    // } elseif (!preg_match('/[0-9]/', $password)) {
    //     $errors[] = "Mật khẩu phải chứa ít nhất 1 số";
    // } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
    //     $errors[] = "Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt";
    // }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    // Check if email already exists
    if (empty($errors)) {
        try {
            $existingUser = db_query_one("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existingUser) {
                $errors[] = "Email này đã được đăng ký";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi kiểm tra email: " . $e->getMessage();
        }
    }
    
    // If no errors, create user
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')";
            $result = db_execute($sql, [$name, $email, $phone, $hashedPassword]);
            
            if ($result) {
                header('Location: login.php');
                exit;
            } else {
                $_SESSION['errors'] = ['Có lỗi xảy ra khi tạo tài khoản'];
                header('Location: register.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = ['Lỗi database: ' . $e->getMessage()];
            header('Location: register.php');
            exit;
        }
    } else {
        $_SESSION['errors'] = $errors;
        header('Location: register.php');
        exit;
    }
} else {
    header('Location: register.php');
    exit;
}
?>