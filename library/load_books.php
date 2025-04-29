<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "library"; // 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 페이지 번호를 받아옴, 기본값은 0 (첫 페이지)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$limit = 5;  // 한 번에 가져올 책 수
$offset = $page * $limit;  // 페이지마다 다른 책을 가져오기 위한 OFFSET

// 책 목록 가져오기
$sql = "SELECT * FROM books ORDER BY created_at DESC LIMIT $limit OFFSET $offset"; 
$result = $conn->query($sql);

$books = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $books[] = [
            "title" => $row["title"],
            "img" => $row["image_url"]  // 책 이미지 URL
        ];
    }
}

// 결과를 JSON 형식으로 반환
echo json_encode($books);

$conn->close();
?>
