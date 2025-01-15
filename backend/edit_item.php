<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'warehouse');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// 獲取請求數據
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$color = $data['color'] ?? null;
$material = $data['material'] ?? null;
$model = $data['model'] ?? null;
$type1 = $data['type1'] ?? null;
$type2 = $data['type2'] ?? null;
$quantity = $data['quantity'] ?? null;

// 驗證數據
if (!$id || !$color || !$material || !$model || !$type1 || !$type2 || !$quantity) {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

// 更新資料
$stmt = $conn->prepare("UPDATE items SET color = ?, material = ?, model = ?, type1 = ?, type2 = ?, quantity = ? WHERE id = ?");
$stmt->bind_param("ssssssi", $color, $material, $model, $type1, $type2, $quantity, $id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Item updated successfully!"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update item"]);
}

$stmt->close();
$conn->close();
?>
