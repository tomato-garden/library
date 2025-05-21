<?php
session_start();
session_unset(); // 모든 세션 변수 제거
session_destroy(); // 세션 파기
header("Location: login.html"); // 로그인 페이지로 이동 (또는 main.php로 변경 가능)
exit();
?>
