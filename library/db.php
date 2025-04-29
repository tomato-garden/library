<?php
$host = "localhost";  // 또는 서버 주소
$dbname = "library";  // 사용 중인 데이터베이스 이름
$username = "root";  // MySQL 사용자 이름
$password = "1234";  // MySQL 비밀번호

// 데이터베이스 연결
$conn = new mysqli($host, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 확인
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}
?>