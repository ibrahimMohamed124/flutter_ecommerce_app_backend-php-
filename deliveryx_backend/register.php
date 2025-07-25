<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once('config.php');

// استقبال البيانات بصيغة JSON
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// تنظيف البيانات
$f_name = htmlspecialchars(trim($data["f_name"] ?? ""));
$l_name = htmlspecialchars(trim($data["l_name"] ?? ""));
$user_name = htmlspecialchars(trim($data["user_name"] ?? ""));
$email = filter_var(trim($data["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($data["phone"] ?? ""));
$password = htmlspecialchars(trim($data["password"] ?? ""));

$errors = [];

// التحقق من الحقول المطلوبة
if (empty($user_name)) $errors["user_name"] = "Username is required";
if (empty($email)) $errors["email"] = "Email is required";
if (empty($password)) $errors["password"] = "Password is required";

// تنفيذ التسجيل إذا لم توجد أخطاء
if (empty($errors)) {
    try {
        // تحقق من عدم وجود الإيميل مسبقًا
        $stmt = $con->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Email already exists"
            ]);
            exit;
        }

        // تشفير كلمة السر
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // إدخال المستخدم في قاعدة البيانات
        $stmt = $con->prepare("INSERT INTO users (f_name, l_name, user_name, email, phone, password, role)
                               VALUES (:f_name, :l_name, :user_name, :email, :phone, :password, 'customer')");

        $stmt->bindParam(":f_name", $f_name);
        $stmt->bindParam(":l_name", $l_name);
        $stmt->bindParam(":user_name", $user_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":password", $hashed_password);

        $stmt->execute();

        echo json_encode([
            "status" => "success",
            "message" => "Registration successful"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    // عرض الأخطاء في حالة وجودها
    echo json_encode([
        "status" => "error",
        "errors" => $errors
    ]);
}
