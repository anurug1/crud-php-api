<?php
@header("Content-Type: application/json; charset=UTF-8");
@header("Access-Control-Allow-Origin: *");
@header("Access-Control-Allow-Methods: POST");

include "config.php";

$content = @file_get_contents("php://input"); //plaintext
$json_data = @json_decode($content, true); // เอา plaintext มาจัดรูปแบบเป็น json (json_decode)
$cus_id = isset($json_data["cus_id"]) ? trim($json_data["cus_id"]) : null;


if (!empty($cus_id)) {
    try {
        $sql = "DELETE FROM customer WHERE cus_id = :cus_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cus_id', $cus_id, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $json = json_encode(["result" => 1, "message" => "customer deleted successfully"]);
        } else {
            $json = json_encode(["result" => 0, "message" => "Failed to delete"]);
        }
    } catch (PDOException $e) {
        $json = json_encode(["result" => 0, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    $json = json_encode(["result" => 0, "message" => "Missing required fields"]);
}

echo $json;
$conn = null;

$ip = $_SERVER['REMOTE_ADDR'];
$date = date("Y-m-d H:i:s");
$message_log = "{$date} {$ip} request:{$content}\nresponse: {$json}\n";
$objFopen = @fopen("log/delete_customer.log", "a+");
@fwrite($objFopen, $message_log);
@fclose($objFopen);
