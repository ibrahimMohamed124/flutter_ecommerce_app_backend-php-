<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once('config.php');

// استقبل البيانات بصيغة JSON
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// تنقية البيانات
$email = filter_var(trim($data["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars(trim($data["password"]));

// التحقق من الحقول
$errors = [];
if (empty($email)) $errors["email"] = "Email is required";
if (empty($password)) $errors["password"] = "Password is required";

// تنفيذ الاستعلام إذا لم توجد أخطاء
if (empty($errors)) {
    try {
        // نجيب المستخدم بالإيميل فقط
        $stmt = $con->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $user = $stmt->fetch();

        // نتحقق من الباسورد باستخدام password_verify
        if ($user && password_verify($password, $user["password"])) {
            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $user["id"],
                    "f_name" => $user["f_name"],
                    "l_name" => $user["l_name"],
                    "user_name" => $user["user_name"],
                    "email" => $user["email"],
                    "phone" => $user["phone"],
                    "role" => $user["role"]
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password"
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "errors" => $errors
    ]);
}
