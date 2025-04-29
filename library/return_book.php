<?php
include 'db.php';

session_start();
$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

$sql = "UPDATE loans 
        SET is_borrowed = 0, return_date = NOW() 
        WHERE user_id = ? AND book_id = ? AND is_borrowed = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "도서가 반납되었습니다."]);
} else {
    echo json_encode(["success" => false, "message" => "반납 처리에 실패했습니다."]);
}
?>
