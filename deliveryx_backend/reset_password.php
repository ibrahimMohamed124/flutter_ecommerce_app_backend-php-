<?php
header("Content-Type: application/json");
require_once("config.php");

// قراءة البيانات
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email)) {
    echo json_encode(["success" => false, "message" => "البريد الإلكتروني مطلوب."]);
    exit;
}

$email = trim($data->email);

// تحقق من وجود المستخدم
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "هذا البريد غير مسجل."]);
    exit;
}

// إنشاء OTP عشوائي من 6 أرقام
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// حفظ OTP في قاعدة البيانات
$update = $conn->prepare("UPDATE users SET reset_otp = ?, otp_expiry = ? WHERE email = ?");
$update->bind_param("sss", $otp, $expiry, $email);
$update->execute();

// إرسال البريد الإلكتروني (مثال بسيط، يفضل استخدام PHPMailer)
$subject = "رمز التحقق لإعادة تعيين كلمة المرور";
$message = "رمز التحقق الخاص بك هو: $otp\nينتهي بعد 10 دقائق.";
$headers = "From: ibrahimrs1234@gmail.com";

mail($email, $subject, $message, $headers);

// الرد
echo json_encode([
    "success" => true,
    "message" => "تم إرسال رمز التحقق إلى بريدك الإلكتروني."
]);
?>
