<?php
// 데이터베이스 연결 설정
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "library";  // 사용하려는 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 체크
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// POST 요청이 왔는지 확인
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // POST 데이터 받기
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);

    // SQL 쿼리 준비
    $sql = "INSERT INTO notices (title, content, author) 
            VALUES ('$title', '$content', '$author')";

    // 쿼리 실행
    if ($conn->query($sql) === TRUE) {
        echo "공지사항이 성공적으로 등록되었습니다.";
    } else {
        echo "에러: " . $sql . "<br>" . $conn->error;
    }
}

// 연결 종료
$conn->close();
?>
