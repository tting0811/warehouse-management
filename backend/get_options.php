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

$type = $_GET['type'];
$response = [];

if ($type === 'material') {
    $result = $conn->query("SELECT value FROM options_material");
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['value'];
    }
} elseif ($type === 'color') {
    $result = $conn->query("SELECT value FROM options_color");
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['value'];
    }
}elseif ($type === 'model_aux') {
    $result = $conn->query("SELECT DISTINCT model_aux FROM options_items");
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['model_aux'];
    }
}elseif ($type === 'type1') {
    $result = $conn->query("SELECT DISTINCT type1 FROM options_items");
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['type1'];
    }
}elseif ($type === 'type2') {
    $result = $conn->query("SELECT DISTINCT type2 FROM options_items");
    while ($row = $result->fetch_assoc()) {
        $response[] = $row['type2'];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
$conn->close();
?>