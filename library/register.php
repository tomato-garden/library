<?php
$servername = "localhost";
$username = "root";  // MySQL 기본 계정
$password = "1234";  // MySQL 비밀번호 (XAMPP 기본값은 빈칸)
$dbname = "library";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
// 연결 체크
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 폼에서 입력받은 값
$user = $_POST['username'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // 비밀번호 암호화

// 중복 체크
$check_sql = "SELECT * FROM users WHERE username='$user' OR email='$email'";
$result = $conn->query($check_sql);
if ($result->num_rows > 0) {
    echo "<script>alert('이미 사용 중인 아이디 또는 이메일입니다.'); history.back();</script>";
    exit;
}

// 회원가입 데이터 삽입
$sql = "INSERT INTO users (username, email, password) VALUES ('$user', '$email', '$pass')";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('회원가입 성공! 로그인 페이지로 이동합니다.'); location.href='login.html';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
