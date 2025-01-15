<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// 連接資料庫
$conn = new mysqli('localhost', 'root', '', 'warehouse');

// 檢查連接
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// 獲取請求數據
$data = json_decode(file_get_contents("php://input"), true);
$color = $data['color'] ?? null;
$material = $data['material'] ?? null;
$model = $data['model'] ?? null;
$type1 = $data['type1'] ?? null;
$type2 = $data['type2'] ?? null;
$quantity = $data['quantity'] ?? null;

// 驗證數據
if (!$color || !$material || !$model || !$type1 || !$type2 || !$quantity) {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

// 分配未使用的標籤
$conn->begin_transaction();
$stmt = $conn->prepare("SELECT label FROM labels WHERE is_used = 0 LIMIT 1 FOR UPDATE");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "No available labels"]);
    exit;
}

$row = $result->fetch_assoc();
$label = $row['label'];

// 標記該標籤為已使用
$update_stmt = $conn->prepare("UPDATE labels SET is_used = 1 WHERE label = ?");
$update_stmt->bind_param("s", $label);
if (!$update_stmt->execute()) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Failed to update label status"]);
    exit;
}

// 插入到 items 表
$insert_stmt = $conn->prepare("INSERT INTO items (label, color, material, model, type1, type2, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insert_stmt->bind_param("ssssssi", $label, $color, $material, $model, $type1, $type2, $quantity);
if ($insert_stmt->execute()) {
    $conn->commit();
    http_response_code(201);
    echo json_encode(["message" => "Item added successfully!"]);
} else {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Failed to add item"]);
}

$stmt->close();
$update_stmt->close();
$insert_stmt->close();
$conn->close();
?>
