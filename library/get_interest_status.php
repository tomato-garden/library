<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("inactive");
}

$user_id = $_SESSION['user_id'];
$book_id = $_GET['book_id'] ?? null;

if (!$book_id) {
    die("inactive");
}

$query = "SELECT status FROM interest_books WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo $row['status'];
} else {
    echo "inactive";
}

$conn->close();
?>
