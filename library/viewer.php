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

// 책 ID를 URL 파라미터로 받기
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // 책 정보 조회 (title, author, content 가져오기)
    $sql = "SELECT title, author, content FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->store_result();
    
    // 책이 존재하면 내용 가져오기
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($title, $author, $content);
        $stmt->fetch();
    } else {
        die("책을 찾을 수 없습니다.");
    }

    $stmt->close();
} else {
    die("책 ID가 제공되지 않았습니다.");
}

// 별점 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']); // 사용자가 선택한 별점
    $user_id = 1; // 로그인 시스템을 이용하는 경우 세션에서 가져올 수 있음. 예시로 1로 설정

    // 리뷰 테이블에 별점 저장
    $sql = "INSERT INTO reviews (book_id, rating, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $book_id, $rating, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>책 뷰어</title>
    <link rel="stylesheet" href="viewer.css">
</head>
<body>
    <div class="header">
        <h3><?php echo htmlspecialchars($title); ?></h3>
        <h5>by <?php echo htmlspecialchars($author); ?></h5>
    </div>
    <button class="close-button" onclick="goBack()">X</button>

    <div class="book-container" id="bookContent"></div>

    <div class="nav-buttons">
        <button onclick="prevPage()">&#x3C;</button>
        <span class="page-indicator" id="pageIndicator">1/1</span>
        <button onclick="nextPage()">&#x3E;</button>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        const bookContent = document.getElementById("bookContent");
        const pageIndicator = document.getElementById("pageIndicator");

        const fullText = <?php echo json_encode($content); ?>.trim();
        let pages = fullText.match(/.{1,500}/g) || [];
        totalPages = pages.length;

        function updatePage() {
            bookContent.textContent = pages[currentPage - 1];
            pageIndicator.textContent = `${currentPage}/${totalPages}`;
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePage();
            }
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePage();
            } else {
                showRatingPopup();
            }
        }

        function showRatingPopup() {
            const popup = document.createElement("div");
            popup.classList.add("rating-popup");
            popup.innerHTML = `
                <div class="popup-content">
                    <h2>별점을 남겨주세요!</h2>
                    <div class="stars">
                        <span class="star" data-value="1">⭐</span>
                        <span class="star" data-value="2">⭐</span>
                        <span class="star" data-value="3">⭐</span>
                        <span class="star" data-value="4">⭐</span>
                        <span class="star" data-value="5">⭐</span>
                    </div>
                    <button id="confirmRating">확인</button>
                </div>
            `;
            document.body.appendChild(popup);

            const stars = popup.querySelectorAll(".star");
            let selectedRating = 0;

            stars.forEach(star => {
                star.addEventListener("click", () => {
                    selectedRating = parseInt(star.getAttribute("data-value"));
                    stars.forEach(s => s.textContent = parseInt(s.getAttribute("data-value")) <= selectedRating ? "🌟" : "⭐");
                });
            });

            document.getElementById("confirmRating").addEventListener("click", () => {
                if (selectedRating > 0) {
                    fetch(window.location.href, {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `rating=${selectedRating}`
                    }).then(response => response.text()).then(data => {
                        if (data === "success") {
                            alert(`감사합니다! 별점: ${selectedRating}`);
                        } else {
                            alert("별점 저장 실패: " + data);
                        }
                        popup.remove();
                    });
                } else {
                    alert("별점을 선택해주세요!");
                }
            });
        }

        function goBack() {
            window.history.back();
        }

        document.addEventListener("DOMContentLoaded", updatePage);
    </script>
</body>
</html>