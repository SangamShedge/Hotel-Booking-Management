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

$room_id = $_GET['id'] ?? null;
$hotel_id = $_SESSION['hotel_id'];

if ($room_id) {
    // Ensure room belongs to the logged-in hotel admin
    $check = $conn->prepare("SELECT status FROM Rooms WHERE room_id = ? AND hotel_id = ?");
    $check->bind_param("ii", $room_id, $hotel_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $room = $result->fetch_assoc();
        $new_status = ($room['status'] === 'Available') ? 'Unavailable' : 'Available';

        $update = $conn->prepare("UPDATE Rooms SET status = ? WHERE room_id = ?");
        $update->bind_param("si", $new_status, $room_id);
        $update->execute();
    }
}

$conn->close();
header("Location: manage_rooms.php");
exit;
?>
