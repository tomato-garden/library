<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "library";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 체크
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// GET 요청으로 받은 아이디 값
$username = $_GET['username'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "unavailable";  // 이미 존재하는 아이디
} else {
    echo "available";  // 사용 가능한 아이디
}

$conn->close();
?>
