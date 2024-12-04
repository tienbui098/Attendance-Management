<?php
// Bắt đầu session
session_start();

// Bao gồm file kết nối đến cơ sở dữ liệu
include 'connect.php';

$error = ''; // Biến để lưu thông báo lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    // Kiểm tra định dạng
    if (empty($username) || empty($password) || empty($email) || empty($address) || empty($phone_number) || empty($role) || empty($gender)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone_number)) {
        $error = "Phone number must be 10 digits.";
    } else {
        // Kiểm tra xem người dùng đã tồn tại chưa
        $stmt_check = $mysqli->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already taken. Please choose another one.";
        } else {
            // Thêm dữ liệu vào bảng Users
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Băm mật khẩu
            $stmt = $mysqli->prepare("INSERT INTO Users (username, password, role, email, address, phone_number, date_of_birth, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $username, $hashed_password, $role, $email, $address, $phone_number, $date_of_birth, $gender);

            if ($stmt->execute()) {
                // Lưu thông báo vào session
                $_SESSION['success_message'] = "Registration successful! Please log in.";
                // Chuyển hướng đến trang đăng nhập
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error; // Hiển thị lỗi nếu có
            }

            $stmt->close();
        }

        $stmt_check->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
        
        *{
            margin: 0;
            padding: 0;
        }

        body{
            position: relative;
            width: 1080px;
            height: 644px;
            background: #FF8001;
        }
        
        h3{
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 9px 20px;

            position: relative;
            width: 1496px;
            height: 30px;

            background: #FFFFFF;
            box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1);
        }

        /* Container */
        .container {
            position: relative;
            width: 100%; /* Đặt lại kích thước để phù hợp với màn hình */
            max-width: 800px; /* Giới hạn kích thước tối đa */
            margin: 0 auto; /* Căn giữa */
            background: #FF8001; /* Màu nền */
            padding: 32px; /* Padding cho khung */
            border-radius: 8px; /* Bo góc cho khung */
        }

        /* Title and Form Flex Container */
        .form-container {
            display: flex;
            align-items: center; /* Căn giữa theo chiều dọc */
            justify-content: space-between; /* Khoảng cách đều giữa tiêu đề và form */
            margin-bottom: 20px; /* Khoảng cách dưới cùng */
            gap: 20px; /* Khoảng cách giữa hai form */
            
        }

        /* Title */
        .form-title h1 {
            position: absolute;
            width: 600px;
            height: 150px;
            left: 40px;
            top: 251px;

            font-family: 'Roboto', sans-serif;
            font-style: normal;

            font-weight: 400;
            font-size: 48px;
            line-height: 77px;
            color: #212529;
            margin: 0;
        }


        /* Form */
        .form-all {
            display: flex;
            gap: 20px; /* Khoảng cách giữa các trường */
            flex-grow: 1; /* Cho phép form chiếm không gian còn lại */
        }

        .content{
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 4px;

            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 48px 49px;
            gap: 26px;

            position: absolute;
            width: 624px;
            height: 450px;
            left: 600px;
            top: 120px;
        }

        .form1, .form2 {
            flex: 1; /* Để cả hai chiếm không gian như nhau */
            min-width: auto; /* Đảm bảo độ rộng tối thiểu */
            display: flex;
            flex-direction: column; /* Xếp các trường theo cột */

        }

        /* Form Group */
        .form-group {
            display: flex;
            flex-direction: column; /* Xếp các label và input theo cột */
        }

        .form-group label {
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 24px;
            color: #212529;
        }

        .form-control {
            width: 100%; /* Đặt độ rộng 100% */
            width: 250px;
            height: 38px; /* Chiều cao cho các trường nhập liệu */
            padding: 8px; /* Padding cho các trường nhập liệu */
            border: 1px solid #CED4DA; /* Đường viền */
            border-radius: 4px; /* Bo góc cho các trường nhập liệu */
            box-sizing: border-box; /* Tính toán kích thước bao gồm padding và border */
        }

        /* Radio Buttons */
        .form-group input[type="radio"] {
            margin-right: 10px; /* Khoảng cách giữa radio và label */
        }

        /* Button */
        .btn {
            background: #4154F1; /* Màu nền cho nút */
            color: #FFFFFF; /* Màu chữ */
            border: none; /* Không có đường viền */
            padding: 10px; /* Padding cho nút */
            border-radius: 4px; /* Bo góc cho nút */
            cursor: pointer; /* Đổi con trỏ khi hover */
            width: 100%; /* Đặt độ rộng 100% */
            font-size: 16px; /* Kích thước chữ */
        }

        .btn:hover {
            background: #3b4dc6; /* Màu nền khi hover */
        }

        /* Link */
        p {
            text-align: center; /* Căn giữa đoạn văn */
        }

        p a {
            color: #0DCAF0; /* Màu cho liên kết */
            text-decoration: none; /* Không gạch chân */
        }

        p a:hover {
            text-decoration: underline; /* Gạch chân khi hover */
        }

        /* Error message */
        .text-danger {
            color: red; /* Màu cho thông báo lỗi */
            font-size: 14px; /* Kích thước chữ cho thông báo lỗi */
        }

    </style>
</head>
<body>
    <h3>Register</h3>

    <div class="container">
        <div class="form-title">
            <h1>Student Attendance Software</h1>
        </div>
        <form class="content" action="" method="POST">
            <div class="form-all">
                <div class="form1">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <span class="text-danger"><?php echo $error; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth:</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <div>
                            <input type="radio" id="admin" name="role" value="admin" required>
                            <label for="admin">Admin</label>
                        </div>
                        <div>
                            <input type="radio" id="teacher" name="role" value="teacher" required>
                            <label for="teacher">Teacher</label>
                        </div>
                        <div>
                            <input type="radio" id="student" name="role" value="student" required>
                            <label for="student">Student</label>
                        </div>
                    </div>
                </div>

                <div class="form2">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <input type="text" class="form-control" id="gender" name="gender" required>
                    </div>
                </div>
            </div>
            <div class="btn-regixter" method="POST">
                <button class="btn btn-success">Register</button>
                <p>Already have an account? <a href="Login.php">Login</a>.</p>
            </div>
        </form>
    </div>
</body>
</html>