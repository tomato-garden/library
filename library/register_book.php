<?php
// DB 연결
$servername = "localhost";  // MySQL 서버 주소
$username = "root";         // MySQL 사용자명
$password = "1234";         // MySQL 비밀번호
$dbname = "library";        // 데이터베이스명

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 폼 데이터 받기
$title = $_POST['title'];
$author = $_POST['author'];
$publisher = $_POST['publisher'];
$image_url = $_POST['image_url'];
$summary = $_POST['summary'];

// 텍스트 파일 처리
$txt_content = ""; // 초기화

if (isset($_FILES['txt_file']) && $_FILES['txt_file']['error'] == 0) {
    // 텍스트 파일이 업로드 되었을 때 처리
    $txt_file = $_FILES['txt_file'];

    // 파일이 제대로 업로드되었는지 확인
    if ($txt_file['size'] > 0) {
        $txt_content = file_get_contents($txt_file['tmp_name']); // 파일 내용 읽기
        
        // 텍스트 내용 인코딩을 UTF-8로 변환
        $txt_content = mb_convert_encoding($txt_content, "UTF-8", "auto");
    } else {
        die("파일 크기가 0입니다. 올바른 텍스트 파일을 업로드해주세요.");
    }
} else {
    die("텍스트 파일을 업로드해야 합니다.");
}

// SQL 쿼리 준비
$sql = "INSERT INTO books (title, author, publisher, image_url, summary, content) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $title, $author, $publisher, $image_url, $summary, $txt_content);

// 실행
if ($stmt->execute()) {
    echo "책 등록이 완료되었습니다.";
} else {
    echo "오류 발생: " . $stmt->error;
}

// 연결 종료
$stmt->close();
$conn->close();
?>
