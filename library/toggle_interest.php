<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("로그인이 필요합니다.");
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? null;

if (!$book_id) {
    die("잘못된 접근입니다.");
}

// 관심 목록에서 도서 삭제
$query = "DELETE FROM interest_books WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "deleted"; // 삭제 성공
} else {
    echo "error"; // 삭제 실패
}

$conn->close();
?>
