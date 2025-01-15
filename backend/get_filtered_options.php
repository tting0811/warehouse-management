<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// 連接到資料庫
$conn = new mysqli('localhost', 'root', '', 'warehouse');

// 檢查連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type = $_GET['type'] ?? '';
$model = $_GET['model'] ?? '';
$type1 = $_GET['type1'] ?? '';
$response = [];

if ($type === 'type1') {
    // 根據型號篩選類型1
    $stmt = $conn->prepare("SELECT DISTINCT type1 FROM options_items WHERE model_aux = ?");
    $stmt->bind_param("s", $model);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['type1'];
    }
    $stmt->close();
} elseif ($type === 'type2') {
    // 根據型號和類型1篩選類型2
    $stmt = $conn->prepare("SELECT DISTINCT type2 FROM options_items WHERE model_aux = ? AND type1 = ?");
    $stmt->bind_param("ss", $model, $type1);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['type2'];
    }
    $stmt->close();
} else {
    http_response_code(400); // 錯誤的請求
    echo json_encode(["error" => "Invalid type parameter"]);
    exit;
}

// 返回 JSON 數據
echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conn->close();
?>