<?php
    @header("Content-Type: application/json; charset=UTF-8");
    @header("Access-Control-Allow-Origin: *");
    @header("Access-Control-Allow-Methods: POST");

    try {
        require_once "config.php";
        $content = @file_get_contents("php://input");
        $json_data = @json_decode($content, true);
        $cus_id = @$json_data["cus_id"];
        $cus_name = @$json_data["cus_name"];
        $cus_username = @$json_data["cus_username"];
        $cus_password = @$json_data["cus_password"];


        if (!empty($cus_id) && !empty($cus_name) && !empty($cus_username) && !empty($cus_password)) {
            // Hash the password before storing
            $hashed_password = password_hash($cus_password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO customer (cus_id, cus_name, cus_username, cus_password) 
                    VALUES (:cus_id, :cus_name, :cus_username, :cus_password)";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':cus_id' => $cus_id,
                ':cus_name' => $cus_name,
                ':cus_username' => $cus_username,
                ':cus_password' => $hashed_password
            ]);

            if ($result) {
                $json = json_encode(["result" => 1, "message" => "Customer added successfully"]);
            } else {
                $json = json_encode(["result" => 0, "message" => "Failed to insert data"]);
            }
        } else {
            $json = json_encode(["result" => 0, "message" => "Missing required fields"]);
        }
    } catch (PDOException $e) {
        $json = json_encode(["result" => 0, "message" => "Database error: " . $e->getMessage()]);
    }

    echo $json;

    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date("Y-m-d H:i:s");
    $message_log = $date." ".$ip." request:".$content."\nresponse: ".$json."\n";
    $objFopen = @fopen("log/insert_customer.log", "a+");
    @fwrite($objFopen, $message_log);
    @fclose($objFopen);
?>
