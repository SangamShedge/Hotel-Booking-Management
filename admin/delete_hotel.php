<?php
session_start();

// Only admin can delete
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// Check if hotel ID is provided
if (!isset($_GET['id'])) {
    die("Hotel ID not provided.");
}

$hotel_id = intval($_GET['id']);

// Database connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally delete associated image
$getImage = $conn->query("SELECT main_image_url FROM Hotels WHERE hotel_id = $hotel_id");
if ($getImage && $getImage->num_rows > 0) {
    $row = $getImage->fetch_assoc();
    $imageFile = $row['main_image_url'];

    if (!empty($imageFile) && file_exists("../uploads/" . $imageFile)) {
        unlink("../uploads/" . $imageFile); // delete image file
    }
}

// Delete hotel from DB
$conn->query("DELETE FROM Hotels WHERE hotel_id = $hotel_id");

$conn->close();

// Redirect back to hotel listing
header("Location: hotels.php");
exit;
?>
