<?php
include 'db.php';

header("Content-Type: application/json; charset=UTF-8");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 조회수 증가
    $stmt = $conn->prepare("UPDATE notices SET view_count = view_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        // 변경된 조회수를 가져와서 반환
        $stmt = $conn->prepare("SELECT view_count FROM notices WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($newViewCount);
        $stmt->fetch();
        $stmt->close();

        echo json_encode(["status" => "success", "view_count" => $newViewCount]);
    } else {
        echo json_encode(["status" => "error", "message" => "조회수 증가 실패"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "잘못된 요청"]);
}

$conn->close();
?>
