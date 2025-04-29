<?php
session_start();
require 'db.php'; // 데이터베이스 연결 파일

// 로그인된 유저 정보 가져오기
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지</title>
    <link rel="stylesheet" href="mypage.css">
</head>
<body>
    <header>
        <div class="logo-search">
            <a href="main.php">
                <img src="./img/logo.png" alt="도서관 로고" class="logo">
            </a>
            <div class="search-box">
                <input type="text" placeholder="검색어 입력">
                <button><img src="./img/search.png"></button>
            </div>
            <button class="mypage-button" onclick="window.location.href='mypage.php'"><img src="./img/mypage.jpg"></button>
        </div>
        <nav>
            <ul>
                <li class="dropdown">
                    <a href="Libraryloan.html">내 서재</a>
                    <ul class="dropdown-menu">
                        <li><a href="Libraryloan.html">대출도서</a></li>
                        <li><a href="return.html">반납도서</a></li>
                        <li><a href="interest.html">관심도서</a></li>
                    </ul>
                </li>
                <li><a href="notice.html">공지사항</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2>마이페이지</h2>
        <div class="content-box">
            <p><strong>아이디:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>이메일:</strong> <?= htmlspecialchars($email) ?></p>
            <div class="password-section">
                <strong>비밀번호:</strong> <span class="password-mask">*******</span>
                <button onclick="togglePasswordChange()">변경</button>
                <div id="password-change-box" class="password-change-box" style="display: none;">
                    <form action="change_password.php" method="POST">
                        <input type="password" name="new_password" placeholder="새 비밀번호" required>
                        <input type="password" name="confirm_password" placeholder="비밀번호 확인" required>
                        <button type="submit">확인</button>
                    </form>
                </div>
                <p id="password-change-message" class="password-change-message">비밀번호 변경이 완료되었습니다.</p>
            </div>
            <div class="scroll-toggle" style="margin-top: 20px;">
                <strong>스크롤 사용</strong>
                <label class="toggle-switch">
                    <input type="checkbox" id="scroll-toggle" onchange="toggleScroll()">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordChange() {
            const changeBox = document.getElementById('password-change-box');
            changeBox.style.display = changeBox.style.display === 'none' ? 'block' : 'none';
        }
    </script>

</body>
</html>
