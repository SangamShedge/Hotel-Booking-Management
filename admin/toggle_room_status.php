<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: rooms.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$room_id = intval($_GET['id']);

// Get current status
$sql = "SELECT status FROM Rooms WHERE room_id = $room_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_status = $row['status'] === 'Available' ? 'Unavailable' : 'Available';

    // Update status
    $update_sql = "UPDATE Rooms SET status = '$new_status' WHERE room_id = $room_id";
    $conn->query($update_sql);
}

$conn->close();
header("Location: rooms.php");
exit;
?>
