<?php
include 'db.php';

session_start();
$user_id = $_SESSION['user_id'];

$sql = "SELECT books.id, books.title, books.author, books.publisher, loans.return_date 
        FROM loans 
        JOIN books ON loans.book_id = books.id 
        WHERE loans.user_id = ? AND loans.is_borrowed = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode($books);
?>
