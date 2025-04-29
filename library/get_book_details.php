<?php
// 데이터베이스 연결
include 'db.php';

// 책 제목을 URL에서 받아옴
$book_title = $_GET['title'];

// 책 정보를 DB에서 가져오기
$query = "SELECT * FROM books WHERE title = '$book_title' LIMIT 1";
$result = mysqli_query($conn, $query);

// 결과가 있으면 책 정보를 JSON 형식으로 반환
if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'book' => [
            'title' => $row['title'],
            'author' => $row['author'],
            'publisher' => $row['publisher'],
            'summary' => $row['summary'],
            'content' => $row['content'],
            'image_url' => $row['image_url']
        ]
    ]);
} else {
    // 제목으로 책을 찾을 수 없는 경우
    echo json_encode(['success' => false, 'message' => '책을 찾을 수 없습니다.']);
}
?>
