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

$room_id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: manage_rooms.php");
exit;
?>
