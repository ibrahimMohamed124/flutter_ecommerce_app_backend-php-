<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include '../config.php';
$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $con->connect_error]);
    exit;
}
// Fetch all categories from the database
$con->set_charset("utf8mb4");

$sql = "SELECT id, name, image FROM categories";
$result = $con->query($sql);

$categories = [];

if ($result && $result instanceof mysqli_result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else if (!$result) {
    echo json_encode(['error' => $con->error]);
    $con->close();
    exit;
}

echo json_encode($categories);

$con->close();
?>
