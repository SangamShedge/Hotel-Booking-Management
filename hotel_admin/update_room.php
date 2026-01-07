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

$room_id = $_POST['room_id'];
$room_type = $_POST['room_type'];
$price = $_POST['price_per_night'];
$max_guests = $_POST['max_guests'];
$available = $_POST['available_count'];
$description = $_POST['description'];

$stmt = $conn->prepare("UPDATE Rooms SET room_type=?, price_per_night=?, max_guests=?, available_count=?, description=? WHERE room_id=?");
$stmt->bind_param("siiisi", $room_type, $price, $max_guests, $available, $description, $room_id);

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: manage_rooms.php");
exit;
?>
