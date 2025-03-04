<?php
try {
    $dsn = "mysql:host=127.0.0.1;dbname=anurug_shop";
    $username = "root";
    $password = "*********";
    
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch(PDOException $e) {
    // Log error instead of displaying it
    error_log("Database connection failed: " . $e->getMessage());
    // Display generic error message
    die("Database connection error");
}
?>