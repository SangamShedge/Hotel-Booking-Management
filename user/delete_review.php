<?php
session_start();

// Check user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Check if review_id is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['review_error'] = "Invalid review ID.";
    header("Location: my_reviews.php");
    exit;
}

$review_id = intval($_GET['id']);

// Ensure the review belongs to the user
$stmt = $conn->prepare("DELETE FROM Reviews WHERE review_id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['review_success'] = "Review deleted successfully.";
} else {
    $_SESSION['review_error'] = "Failed to delete review or unauthorized action.";
}

$stmt->close();
$conn->close();

header("Location: my_reviews.php");
exit;
?>