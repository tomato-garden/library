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

// 별점 평균을 기준으로 책을 5개만 가져옴
$sql = "
    SELECT b.id, b.title, b.image_url, 
           AVG(r.rating) AS avg_rating
    FROM books b
    LEFT JOIN reviews r ON b.id = r.book_id
    GROUP BY b.id
    ORDER BY avg_rating DESC
    LIMIT 5
";

$result = $conn->query($sql);

$recommended_books = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recommended_books[] = [
            "title" => $row["title"],
            "img" => $row["image_url"],
            "avg_rating" => $row["avg_rating"] // 평균 별점
        ];
    }
}

// 결과를 JSON 형식으로 반환
echo json_encode($recommended_books);

$conn->close();
?>
