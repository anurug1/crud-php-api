<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once "config.php";

$content = file_get_contents("php://input");
$json_data = json_decode($content, true);
$cus_id = $json_data["cus_id"] ?? '';
$cus_name = $json_data["cus_name"] ?? '';
$cus_username = $json_data["cus_username"] ?? '';
$cus_password = $json_data["cus_password"] ?? '';

if (!empty($cus_id) && !empty($cus_name) && !empty($cus_username) && !empty($cus_password)) {
    try {
        $sql = "UPDATE customer SET cus_name=:cus_name, cus_password=:cus_password WHERE cus_id=:cus_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cus_name', $cus_name);
        $stmt->bindParam(':cus_password', $cus_password);
        $stmt->bindParam(':cus_id', $cus_id);

        if ($stmt->execute()) {
            $json = json_encode(["result" => 1, "message" => "Customer updated successfully"]);
        } else {
            $json = json_encode(["result" => 0, "message" => "Failed to update"]);
        }
    } catch (PDOException $e) {
        $json = json_encode(["result" => 0, "message" => "Database error"]);
    }
} else {
    $json = json_encode(["result" => 0, "message" => "Missing required fields"]);
}

echo $json;

$ip = $_SERVER['REMOTE_ADDR'];
$date = date("Y-m-d H:i:s");
$message_log = $date . " " . $ip . " request:" . $content . "\nresponse: " . $json . "\n";
$objFopen = fopen("log/update_customer.log", "a+");
fwrite($objFopen, $message_log);
fclose($objFopen);
?>