<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode([]));
}

$user_id = $_SESSION['user_id'];

$query = "SELECT books.id, books.title, books.author, books.publisher 
          FROM interest_books 
          JOIN books ON interest_books.book_id = books.id
          WHERE interest_books.user_id = ? AND interest_books.status = 'active'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode($books);
$conn->close();
?>
