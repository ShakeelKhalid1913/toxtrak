<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validate inputs
    $error = '';
    
    if (empty($email)) {
        $error = "Please login to access the dashboard.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } elseif ($email === 'admin@gmail.com' && $password === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_email'] = $email;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
    
    $_SESSION['error'] = $error;
    header("Location: index.php");
    exit();
}
?>
