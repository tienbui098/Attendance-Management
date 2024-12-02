<?php
// Bật báo cáo lỗi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Thông tin cấu hình kết nối
$host = "localhost";
$user = "root";
$password = "";
$database = "attendance1";

try {
    // Kết nối đến cơ sở dữ liệu
    $mysqli = new mysqli($host, $user, $password, $database);
    
    // Thiết lập charset
    $mysqli->set_charset("utf8");

    // Nếu kết nối thành công, bạn có thể thực hiện các truy vấn ở đây
    // echo "Connected successfully"; // Có thể bỏ dòng này nếu không cần

} catch (mysqli_sql_exception $e) {
    // Xử lý lỗi kết nối
    echo "Connection failed: " . $e->getMessage();
    exit();
}

?>
