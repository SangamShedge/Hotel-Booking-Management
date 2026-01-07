<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$user_id = $_SESSION['user_id'];
$hotel_id = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate
$errors = [];
if ($hotel_id <= 0) $errors[] = "Invalid hotel.";
if ($rating < 1 || $rating > 5) $errors[] = "Rating must be between 1 and 5.";
if (empty($comment)) $errors[] = "Comment cannot be empty.";

// Check for duplicate review
$check = $conn->prepare("SELECT review_id FROM Reviews WHERE user_id = ? AND hotel_id = ?");
$check->bind_param("ii", $user_id, $hotel_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $errors[] = "You have already reviewed this hotel.";
}

if (!empty($errors)) {
    $_SESSION['review_error'] = implode("<br>", $errors);
    header("Location: my_reviews.php");
    exit;
}

// Insert review
$stmt = $conn->prepare("INSERT INTO Reviews (user_id, hotel_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $hotel_id, $rating, $comment);

if ($stmt->execute()) {
    $_SESSION['review_success'] = "Review added successfully.";
} else {
    $_SESSION['review_error'] = "Failed to submit review.";
}

header("Location: my_reviews.php");
$conn->close();
exit;
?>
