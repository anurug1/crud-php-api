<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
require("config.php");

try {
    // ตรวจสอบว่าเป็นคำขอแบบ POST เท่านั้น
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode([
            "result" => 0,
            "message" => "Method Not Allowed",
            "datalist" => null
        ]);
        exit();
    }

    // รับข้อมูล JSON จาก client
    $content = file_get_contents("php://input");
    $json_data = json_decode($content, true);

    // ตรวจสอบว่าข้อมูล JSON ถูกต้อง
    if ($json_data === null) {
        echo json_encode([
            "result" => 0,
            "message" => "Invalid JSON",
            "datalist" => null
        ]);
        exit();
    }

    // ดึงค่า username และ password
    $username = isset($json_data["username"]) ? trim($json_data["username"]) : '';
    $password = isset($json_data["password"]) ? trim($json_data["password"]) : '';

    // ตรวจสอบว่า username และ password ไม่เป็นค่าว่าง
    if ($username === '' || $password === '') {
        echo json_encode([
            "result" => 0,
            "message" => "Username and password are required",
            "datalist" => null
        ]);
        exit();
    }

    // คำสั่ง SQL: เลือกเฉพาะคอลัมน์ที่จำเป็น
    $stmt = $conn->prepare("SELECT cus_id, cus_name, cus_password FROM customer WHERE cus_username = :username");
    $stmt->execute([':username' => $username]);

    // ตรวจสอบผลลัพธ์
    if ($stmt->rowCount() === 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบว่ามีค่ารหัสผ่านและตรงกับที่ป้อนมาหรือไม่
        if ($row && isset($row['cus_password']) && password_verify($password, $row['cus_password'])) {
            $result = 1;
            $message = "ok";
            $datalist = [
                "cus_id" => $row['cus_id'],
                "cus_name" => $row['cus_name']
            ];
        } else {
            $result = 0;
            $message = "login-fail";
            $datalist = null;
        }
    } else {
        $result = 0;
        $message = "login-fail";
        $datalist = null;
    }

    // ส่งผลลัพธ์เป็น JSON
    $response = json_encode([
        "result" => $result,
        "message" => $message,
        "datalist" => $datalist
    ]);
    echo $response;

    // บันทึก Log การเข้าใช้งาน
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date("Y-m-d H:i:s");
    $message_log = "$date $ip request: $content\nresponse: $response\n";

    if (!@file_put_contents("log/get_login.log", $message_log, FILE_APPEND)) {
        error_log("Failed to write log to get_login.log");
    }

} catch (PDOException $e) {
    echo json_encode([
        "result" => 0,
        "message" => "Database error",
        "datalist" => null
    ]);
}

exit();
?>