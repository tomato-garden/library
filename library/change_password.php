<?php
session_start();
require 'db.php'; // DB 연결

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    echo "<script>alert('비밀번호가 일치하지 않습니다. 다시 입력해주세요.'); history.back();</script>";
    exit();
}

// 비밀번호 암호화 (해싱)
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// DB에 비밀번호 업데이트
$query = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $hashed_password, $user_id);
if ($stmt->execute()) {
    echo "<script>alert('비밀번호 변경이 완료되었습니다.'); window.location.href='mypage.php';</script>";
} else {
    echo "<script>alert('비밀번호 변경에 실패했습니다.'); history.back();</script>";
}
$stmt->close();
?>
