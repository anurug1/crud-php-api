<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

try {
    require_once "config.php";

    $sql = "SELECT cus_id, cus_name, cus_username FROM customer";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["result" => 1, "customers" => $customers]);

} catch(PDOException $e) {
    echo json_encode(["result" => 0, "error" => $e->getMessage()]);
}

?>
