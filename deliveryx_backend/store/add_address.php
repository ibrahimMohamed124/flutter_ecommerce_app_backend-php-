<?php
header("Content-Type: application/json");
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $user_id = $data['user_id'] ?? null;
    $name = $data['name'] ?? '';
    $phone = $data['phone'] ?? '';
    $street = $data['street'] ?? '';
    $postal_code = $data['postal_code'] ?? '';
    $city = $data['city'] ?? '';
    $state = $data['state'] ?? '';
    $country = $data['country'] ?? '';

    if ($user_id && $name && $phone) {
        try {
            $sql = "INSERT INTO user_addresses (user_id, name, phone, street, postal_code, city, state, country) 
                    VALUES (:user_id, :name, :phone, :street, :postal_code, :city, :state, :country)";
            $stmt = $con->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':name' => $name,
                ':phone' => $phone,
                ':street' => $street,
                ':postal_code' => $postal_code,
                ':city' => $city,
                ':state' => $state,
                ':country' => $country
            ]);

            echo json_encode(["status" => "success", "message" => "Address added successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Failed to add address: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
}
