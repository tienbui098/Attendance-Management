<?php
session_start();

// Kết nối đến cơ sở dữ liệu
$mysqli = new mysqli("localhost", "root", "", "attendance1");

// Kiểm tra kết nối
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    if (!empty($username) && !empty($password)) {
        // Sử dụng prepared statements để tránh SQL injection
        $sql = "SELECT * FROM Users WHERE Username=? AND Password=?";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                // Đăng nhập thành công
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                header("Location: main.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
            
            // Đóng statement
            $stmt->close();
        } else {
            // Nếu không thể chuẩn bị câu lệnh
            $error = "Could not prepare statement.";
        }
    } else {
        $error = "Please enter username and password.";
    }
}

// Đóng kết nối
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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

        .title{
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

        .form-title{
            position: absolute;
            width: 669px;
            height: 154px;
            left: 70px;
            top: 280px;

            font-family: 'Roboto';
            font-style: normal;
            font-weight: 400;
            font-size: 28px;
            line-height: 77px;

            color: #212529;
        }

        .form{
            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 48px 49px;
            gap: 26px;

            position: absolute;
            width: 624px;
            height: 377px;
            left: 777px;
            top: 174px;

            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 4px;
        }

        .form-group {
            position: relative;
            margin-bottom: 30px;
        }

        .form-group label {
            position: absolute;
            top: -20px;
            left: 0;
            font-size: 14px;
            font-weight: 400;
            color: #212529;
        }

        .form-group .form-control {
            width: 450px;
            height: 24px;
            padding: 8px 12px;
            font-size: 15px;
            border: 1px solid #CED4DA;
            border-radius: 4px;
            background-color: #FFFFFF;
        }

        .text-danger {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .btn {
            display: block;
            width: 30%;
            height: 38px;
            padding: 8px 12px;
            font-size: 16px;
            font-weight: 400;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-success {
            background-color: #4154F1;
            color: #FFFFFF;
            border: none;
        }

        .btn-success:hover {
            background-color: #2540d9;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        .register-link a {
            color: #0DCAF0;
            text-decoration: none;
        }

        .register-link a:hover {
            color: #0aa8c2;
        }

    </style>
</head>

<body>
    <div class="title">
    <p>Login form</p>
    </div>

    <div class="container">
        <div class="form-title">
            <h1>Student Attendance  Software</h1>
        </div>

        <div class="form" action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span class="text-danger"><?php echo $error; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="text-danger"><?php echo $error; ?></span>
            </div>
            <button class="btn btn-success">Login</button>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </div>
    </div>
</body>

</html>