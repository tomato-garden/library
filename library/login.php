<?php
session_start();
$host = "localhost"; // DB 호스트
$user = "root"; // DB 사용자
$pass = "1234"; // DB 비밀번호
$dbname = "library"; // 사용자의 데이터베이스 이름

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

// 연결 확인
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL Injection 방지를 위한 Prepared Statement 사용
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "아이디 또는 비밀번호가 일치하지 않습니다."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "아이디 또는 비밀번호가 일치하지 않습니다."]);
    }

    $stmt->close();
}

$conn->close();
?>
