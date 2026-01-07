<?php
session_start();

if (!isset($_SESSION['hotel_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hotel_id = $_SESSION['hotel_id'];
$name = $_POST['name'];
$location = $_POST['location'];
$description = $_POST['description'];

// Handle image upload
$image = $_FILES['main_image']['name'];
$tmp = $_FILES['main_image']['tmp_name'];

if (!empty($image)) {
    $target = "../uploads/" . basename($image);
    move_uploaded_file($tmp, $target);
    $sql = "UPDATE Hotels SET name=?, location=?, description=?, main_image_url=? WHERE hotel_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $location, $description, $image, $hotel_id);
} else {
    $sql = "UPDATE Hotels SET name=?, location=?, description=? WHERE hotel_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $location, $description, $hotel_id);
}

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit;
?>
