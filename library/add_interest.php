<?php
include 'db.php'; // DB 연결

session_start();
if (!isset($_SESSION['user_id'])) {
    die("로그인이 필요합니다.");
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? null;

if (!$book_id) {
    die("잘못된 접근입니다.");
}

// 중복 확인
$query = "SELECT * FROM interest_books WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("이미 관심 도서에 추가되었습니다.");
}

// 관심 도서 추가
$query = "INSERT INTO interest_books (user_id, book_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}

$conn->close();
?>

