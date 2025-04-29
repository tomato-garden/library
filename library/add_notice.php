<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $author = $_POST["author"];

    $stmt = $conn->prepare("INSERT INTO notices (title, content, author) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $author);

    if ($stmt->execute()) {
        echo "공지사항이 성공적으로 추가되었습니다.";
    } else {
        echo "오류 발생: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
