<?php
session_start();

// Xóa các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng người dùng về trang login
header("Location: Login.php");
exit();
?>