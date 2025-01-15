<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=utf-8");

// 連接到資料庫
$conn = new mysqli('localhost', 'root', '', 'warehouse');

// 檢查連接
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 查詢 items 資料
$result = $conn->query("SELECT * FROM items ORDER BY created_at DESC");

// 生成表格行
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['label']}</td>
            <td>{$row['color']}</td>
            <td>{$row['material']}</td>
            <td>{$row['model']}</td>
            <td>{$row['type1']}</td>
            <td>{$row['type2']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['created_at']}</td>
            <td>
                <button class='btn btn-warning btn-sm me-2'><i class='fas fa-edit'></i> 編輯</button>
                <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['id']}'><i class='fas fa-trash'></i> 刪除</button>
            </td>
          </tr>";
}

$conn->close();
?>
