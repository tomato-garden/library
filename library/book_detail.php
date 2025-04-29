<?php
session_start(); // 세션 시작

// 데이터베이스 연결
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// 연결 오류 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "로그인이 필요합니다.";
    exit;
}

$user_id = $_SESSION['user_id']; // 로그인된 사용자 ID

// 관심 도서 추가/제거 처리
if (isset($_POST['interest'])) {
    if (isset($_POST['book_id'])) {
        $book_id = $_POST['book_id'];

        // 중복 확인
        $check_sql = "SELECT * FROM interest_books WHERE user_id = ? AND book_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // 관심 도서 추가
            $sql = "INSERT INTO interest_books (user_id, book_id, status) VALUES (?, ?, 'active')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $book_id);
            if ($stmt->execute()) {
                echo "<script>alert('관심 도서에 추가되었습니다.');</script>";
            } else {
                echo "<script>alert('관심 도서 추가에 실패했습니다.');</script>";
            }
            $stmt->close();
        } else {
            // 관심 도서 취소
            $sql = "UPDATE interest_books SET status = 'inactive' WHERE user_id = ? AND book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $book_id);
            if ($stmt->execute()) {
                echo "<script>alert('관심 도서가 취소되었습니다.');</script>";
            } else {
                echo "<script>alert('관심 도서 취소에 실패했습니다.');</script>";
            }
            $stmt->close();
        }
    }
}

// 대출 처리
if (isset($_POST['borrow'])) {
    if (isset($_POST['book_id'])) {
        $book_id = $_POST['book_id'];

        // 대출 상태 확인
        $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // 대출 처리
            $sql = "INSERT INTO loans (user_id, book_id, is_borrowed) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $book_id);
            if ($stmt->execute()) {
                echo "<script>alert('대출되었습니다.');</script>";
            } else {
                echo "<script>alert('대출에 실패했습니다.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('이미 대출 중인 도서입니다.');</script>";
        }
    }
}

// 반납 처리
if (isset($_POST['return'])) {
    if (isset($_POST['book_id'])) {
        $book_id = $_POST['book_id'];

        // 대출 상태 확인
        $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // 반납 처리
            $sql = "UPDATE loans SET is_borrowed = 0 WHERE user_id = ? AND book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $book_id);
            if ($stmt->execute()) {
                echo "<script>alert('반납되었습니다.');</script>";
            } else {
                echo "<script>alert('반납에 실패했습니다.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('대출되지 않은 도서입니다.');</script>";
        }
    }
}

// 책 정보 가져오기
if (isset($_GET['title'])) {
    $book_title = mysqli_real_escape_string($conn, $_GET['title']); // SQL 인젝션 방지

    // 책 정보를 가져오는 SQL 쿼리
    $sql = "SELECT * FROM books WHERE title = '$book_title'";
    $result = $conn->query($sql);

    // 책 정보가 존재하면 출력
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $title = $book['title'];
        $author = $book['author'];
        $publisher = $book['publisher'];
        $summary = $book['summary'];
        $image_path = $book['image_url']; // 책 이미지 경로
        $book_id = $book['id'];

        // 대출 상태 확인
        $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        // 대출된 책인지 확인
        if ($check_result->num_rows > 0) {
            $borrow_button_text = "읽기";  // 대출 상태인 경우 '읽기' 버튼
        } else {
            $borrow_button_text = "대출";  // 대출 상태가 아닌 경우 '대출' 버튼
        }

        // 관심 도서 상태 확인
        $interest_sql = "SELECT * FROM interest_books WHERE user_id = ? AND book_id = ?";
        $interest_stmt = $conn->prepare($interest_sql);
        $interest_stmt->bind_param("ii", $user_id, $book_id);
        $interest_stmt->execute();
        $interest_result = $interest_stmt->get_result();
        $interest_status = '👀'; // 기본 비활성화 상태

        if ($interest_result->num_rows > 0) {
            $interest_row = $interest_result->fetch_assoc();
            if ($interest_row['status'] == 'active') {
                $interest_status = '🐸';  // 활성화 상태
            }
        }
    } else {
        echo "책을 찾을 수 없습니다.";
        exit;
    }
} else {
    echo "책 제목이 제공되지 않았습니다.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>도서 상세 페이지</title>
    <link rel="stylesheet" href="detail.css">
</head>
<body>
    <header>
        <div class="logo-search">
            <img src="./img/logo.png" alt="도서관 로고" class="logo">
            <div class="search-box">
                <input type="text" placeholder="검색어 입력">
                <button><img src="./img/search.png"></button>
            </div>
            <button class="mypage-button" onclick="location.href='mypage.html'"><img src="./img/mypage.jpg"></button>
        </div>
        <nav>
            <ul>
                <li class="dropdown">
                    <a href="#">내 서재</a>
                    <ul class="dropdown-menu">
                        <li><a href="Libraryloan.html">대출도서</a></li>
                        <li><a href="return.html">반납도서</a></li>
                        <li><a href="interest.html">관심도서</a></li>
                    </ul>
                </li>
                <li>
                    <a href="notice.html">공지사항</a>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="book-info">
            <div class="book-image">
                <!-- 책 이미지 경로를 동적으로 출력 -->
                <img id="book-image" src="<?php echo $image_path; ?>" alt="책 표지">
                <button class="view-button">크게 보기</button>
            </div>
            <div class="de-button-con">
                <div class="details">
                    <!-- 책 제목, 작가, 출판사 정보를 동적으로 출력 -->
                    <h2><?php echo $title; ?></h2>
                    <p><strong>작가:</strong> <?php echo $author; ?> 저자(글)</p>
                    <p><?php echo $publisher; ?></p>
                </div>
                <div class="buttons">
                    <!-- 대출 버튼 -->
                    <form method="post">
                        <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                        <?php if ($borrow_button_text == "읽기") { ?>
                            <a href="viewer.php?book_id=<?php echo $book_id; ?>">
                                <button type="button">읽기</button>
                            </a>
                        <?php } else { ?>
                            <button type="submit" name="borrow">대출</button>
                        <?php } ?>
                        <button type="submit" name="return">반납</button>
                        <!-- 관심 버튼 -->
                <button type="submit" name="interest" id="interestButton"><?php echo $interest_status; ?></button>
                    </form>
                
                    <button id="readingButton" style="display: none;">읽기</button>
                </div>
            </div>
        </div>
        <div class="summary">
            <h3>줄거리</h3>
            <!-- 줄거리 출력 -->
            <p id="summaryText"><?php echo nl2br($summary); ?></p>
            <!-- 자세히 보기 버튼, 5줄 초과 시에만 나타남 -->
            <button id="moreButton" style="display: none;">자세히 보기</button>
        </div>
    </div>

    <script>
   document.addEventListener("DOMContentLoaded", function () {
    // 1. 로고 클릭 시 메인 페이지로 이동
    document.querySelector(".logo").addEventListener("click", function () {
        window.location.href = "main.html";
    });

    // 2. 내 서재 메뉴 클릭 시 페이지 이동 처리
    document.querySelectorAll(".dropdown-menu li a").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const text = this.textContent.trim();
            if (text === "대출도서") {
                window.location.href = "Libraryloan.html";
            } else if (text === "반납도서") {
                window.location.href = "return.html";
            } else if (text === "관심도서") {
                window.location.href = "interest.html";
            }
        });
    });

    // 3. '크게 보기' 클릭 시 책 이미지 확대
    document.querySelector(".view-button").addEventListener("click", function () {
        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = "0";
        overlay.style.left = "0";
        overlay.style.width = "100vw";
        overlay.style.height = "100vh";
        overlay.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
        overlay.style.display = "flex";
        overlay.style.alignItems = "center";
        overlay.style.justifyContent = "center";
        overlay.style.zIndex = "200";

        const bookImage = document.querySelector(".book-image img");
        const img = document.createElement("img");
        img.src = bookImage.src;
        img.alt = bookImage.alt;
        img.style.maxWidth = "80%";
        img.style.maxHeight = "80%";
        img.style.borderRadius = "10px";
        img.style.boxShadow = "0 0 10px white";

        overlay.appendChild(img);
        document.body.appendChild(overlay);

        overlay.addEventListener("click", function () {
            document.body.removeChild(overlay);
        });
    });

    // 4. 줄거리 '자세히 보기' 기능 구현
    const summaryText = document.getElementById('summaryText');
    const moreButton = document.getElementById('moreButton');
    
    const fullText = summaryText.innerText;
    const lines = fullText.split('.');

    if (lines.length > 5) {
        moreButton.style.display = 'inline-block';
        moreButton.style.padding = '8px 12px';
        moreButton.style.border = 'none';
        moreButton.style.borderRadius = '5px';
        moreButton.style.backgroundColor = '#48A6A7';
        moreButton.style.color = 'white';
        moreButton.style.fontSize = '14px';
        moreButton.style.cursor = 'pointer';
        moreButton.style.transition = 'background-color 0.3s';

        moreButton.addEventListener("mouseover", function () {
            moreButton.style.backgroundColor = "#006A71";
        });

        moreButton.addEventListener("mouseout", function () {
            moreButton.style.backgroundColor = "#48A6A7";
        });

        summaryText.innerText = lines.slice(0, 5).join('.') + '.';

        let isExpanded = false;

        moreButton.addEventListener('click', function () {
            if (!isExpanded) {
                summaryText.innerText = fullText;
                moreButton.innerText = "줄이기";
            } else {
                summaryText.innerText = lines.slice(0, 5).join('.') + '.';
                moreButton.innerText = "자세히 보기";
            }
            isExpanded = !isExpanded;
        });
    }
});

</script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            const interestButton = document.getElementById("interestButton");
            
            // 관심 버튼 클릭 시 상태 변경
            interestButton.addEventListener("click", function () {
                if (interestButton.textContent === "🐸") {
                    interestButton.textContent = "👀";  // 비활성화 상태로 변경
                } else {
                    interestButton.textContent = "🐸";  // 활성화 상태로 변경
                }
            });
        });
    </script>
</body>
</html>