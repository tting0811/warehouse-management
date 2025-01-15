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
$id = $data['id'] ?? null;

// 驗證數據
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid ID"]);
    exit;
}

// 查詢貨物標籤
$conn->begin_transaction();
$stmt = $conn->prepare("SELECT label FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $conn->rollback();
    http_response_code(404);
    echo json_encode(["error" => "Item not found"]);
    exit;
}

$row = $result->fetch_assoc();
$label = $row['label'];

// 刪除資料
$stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["message" => "Item deleted successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete item"]);
}

// 釋放標籤
$update_stmt = $conn->prepare("UPDATE labels SET is_used = 0 WHERE label = ?");
$update_stmt->bind_param("s", $label);
if (!$update_stmt->execute()) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Failed to release label"]);
    exit;
}

$stmt->close();
$conn->close();
?>
