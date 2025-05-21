<?php
session_start();

// 데이터베이스 연결
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}



$user_id = $_SESSION['user_id'];

$status_message = '';

// 책 제목 받아오기
$book_title = isset($_GET['title']) ? mysqli_real_escape_string($conn, $_GET['title']) : '';

if (isset($_POST['interest']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    $check_sql = "SELECT * FROM interest_books WHERE user_id = ? AND book_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        $sql = "INSERT INTO interest_books (user_id, book_id, status) VALUES (?, ?, 'active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
    } else {
        $row = $check_result->fetch_assoc();
        $new_status = ($row['status'] === 'active') ? 'inactive' : 'active';

        $sql = "UPDATE interest_books SET status = ? WHERE user_id = ? AND book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $new_status, $user_id, $book_id);
        $stmt->execute();
    }

    header("Location: ".$_SERVER['PHP_SELF']."?title=".$book_title);
    exit;
}

// 대출
if (isset($_POST['borrow']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        $sql = "INSERT INTO loans (user_id, book_id, is_borrowed) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
    }

    header("Location: ".$_SERVER['PHP_SELF']."?title=".$book_title."&status=borrowed");
    exit;
}

// 반납
if (isset($_POST['return']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $book_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $sql = "UPDATE loans SET is_borrowed = 0, return_date = NOW() WHERE user_id = ? AND book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
    }    

    header("Location: ".$_SERVER['PHP_SELF']."?title=".$book_title."&status=returned");
    exit;
}

// 알림 메시지 처리
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'borrowed') {
        $status_message = '대출이 완료되었습니다.';
    } elseif ($_GET['status'] === 'returned') {
        $status_message = '반납이 완료되었습니다.';
    }
}

// 책 정보 가져오기
if ($book_title) {
    $sql = "SELECT * FROM books WHERE title = '$book_title'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $title = $book['title'];
        $author = $book['author'];
        $publisher = $book['publisher'];
        $summary = $book['summary'];
        $image_path = $book['image_url'];
        $book_id = $book['id'];

        $check_sql = "SELECT * FROM loans WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        $borrow_button_text = ($check_result->num_rows > 0) ? "읽기" : "대출";

        $interest_sql = "SELECT * FROM interest_books WHERE user_id = ? AND book_id = ?";
        $interest_stmt = $conn->prepare($interest_sql);
        $interest_stmt->bind_param("ii", $user_id, $book_id);
        $interest_stmt->execute();
        $interest_result = $interest_stmt->get_result();
        $interest_status = '👀';

        if ($interest_result->num_rows > 0) {
            $interest_row = $interest_result->fetch_assoc();
            if ($interest_row['status'] == 'active') {
                $interest_status = '🐸';
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
    <title>도서 상세 페이지</title>
    <link rel="stylesheet" href="detail.css">
</head>
<body>
<header>
    <div class="logo-search">
        <img src="./img/logo.png" alt="도서관 로고" class="logo" onclick="location.href='main.html'">
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
            <li><a href="notice.html">공지사항</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <div class="book-info">
        <div class="book-image">
            <img id="book-image" src="<?php echo $image_path; ?>" alt="책 표지">
            <button class="view-button">크게 보기</button>
        </div>
        <div class="de-button-con">
            <div class="details">
                <h2><?php echo $title; ?></h2>
                <p><strong>작가:</strong> <?php echo $author; ?> 저자(글)</p>
                <p><?php echo $publisher; ?></p>
            </div>
            <div class="buttons">
                <form method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <?php if ($borrow_button_text == "읽기") { ?>
                        <a href="viewer.php?book_id=<?php echo $book_id; ?>"><button type="button">읽기</button></a>
                    <?php } else { ?>
                        <button type="submit" name="borrow">대출</button>
                    <?php } ?>
                    <button type="submit" name="return" id="returnButton">반납</button>
                    <button type="submit" name="interest" id="interestButton"><?php echo $interest_status; ?></button>
                </form>
            </div>
        </div>
    </div>
    <div class="summary">
        <h3>줄거리</h3>
        <p id="summaryText"><?php echo nl2br($summary); ?></p>
    </div>
</div>

<script>
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

    const img = document.createElement("img");
    img.src = document.querySelector(".book-image img").src;
    img.style.maxWidth = "80%";
    img.style.maxHeight = "80%";
    img.style.borderRadius = "10px";
    img.style.boxShadow = "0 0 10px white";

    overlay.appendChild(img);
    document.body.appendChild(overlay);
    overlay.addEventListener("click", () => document.body.removeChild(overlay));
});
</script>

<?php if (!empty($status_message)) : ?>
<script>
    alert("<?php echo $status_message; ?>");
</script>
<?php endif; ?>
<script>
const isBorrowed = <?php echo ($borrow_button_text === "읽기") ? 'true' : 'false'; ?>;

document.getElementById('returnButton').addEventListener('click', function(event) {
    if (!isBorrowed) {
        event.preventDefault();
        alert('대출을 먼저 진행해주세요.');
    } else {
        // 기존 confirm 넣고 싶으면 여기서 처리 가능
        if (!confirm('반납하시겠습니까?')) {
            event.preventDefault();
        }
    }
});
</script>
</body>
</html>
