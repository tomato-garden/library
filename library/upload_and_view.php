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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 폼 데이터 받기
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $image_url = $_POST['image_url'];
    $summary = $_POST['summary'];

    // 텍스트 파일 처리
    if (isset($_FILES['txt_file']) && $_FILES['txt_file']['error'] == 0) {
        $txt_file = $_FILES['txt_file'];
        $txt_content = file_get_contents($txt_file['tmp_name']);  // 파일 내용을 읽음
        
        // 파일 내용이 UTF-8로 저장되도록 처리
        $txt_content = mb_convert_encoding($txt_content, "UTF-8", "auto");

        // SQL 쿼리 준비
        $sql = "INSERT INTO books (title, author, publisher, image_url, summary, content) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $title, $author, $publisher, $image_url, $summary, $txt_content);

        if ($stmt->execute()) {
            echo "책 등록이 완료되었습니다.";
            header("Location: viewer.php?book_id=" . $conn->insert_id);  // 등록 후 뷰어 페이지로 리다이렉트
            exit();
        } else {
            echo "오류 발생: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "텍스트 파일을 업로드해야 합니다.";
    }
}

$conn->close();
?>
