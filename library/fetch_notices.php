<?php
include 'db.php';

$sql = "SELECT id, title, content, author, view_count, created_at FROM notices ORDER BY created_at DESC";
$result = $conn->query($sql);

$notices = [];

while ($row = $result->fetch_assoc()) {
    $notices[] = $row;
}

echo json_encode($notices, JSON_UNESCAPED_UNICODE);
?>
