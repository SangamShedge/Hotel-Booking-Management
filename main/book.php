<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to book a room.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $room_id = (int) $_POST['room_id'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];

    // Fetch room details
    $roomQuery = $conn->prepare("SELECT price_per_night, available_count FROM Rooms WHERE room_id = ?");
    $roomQuery->bind_param("i", $room_id);
    $roomQuery->execute();
    $roomResult = $roomQuery->get_result();

    if ($roomResult->num_rows === 0) {
        echo "Invalid room selected.";
        exit;
    }

    $room = $roomResult->fetch_assoc();
    $price_per_night = $room['price_per_night'];
    $available_count = $room['available_count'];

    if ($available_count <= 0) {
        echo "Sorry, this room is currently unavailable.";
        exit;
    }

    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    if ($nights < 1) {
        echo "Check-out must be after check-in.";
        exit;
    }

    $total_price = $nights * $price_per_night;

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO Bookings (user_id, room_id, check_in_date, check_out_date, total_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $user_id, $room_id, $check_in, $check_out, $total_price);
    
    if ($stmt->execute()) {
        echo "<h2>Booking Confirmed!</h2><p>Your booking ID is " . $stmt->insert_id . ".</p>";

        // Update available_count
        $newCount = $available_count - 1;

        if ($newCount <= 0) {
            // Set status to Unavailable
            $update = $conn->prepare("UPDATE Rooms SET available_count = 0, status = 'Unavailable' WHERE room_id = ?");
        } else {
            $update = $conn->prepare("UPDATE Rooms SET available_count = ? WHERE room_id = ?");
            $update->bind_param("ii", $newCount, $room_id);
        }

        if (!$update->execute()) {
            echo "<p>Warning: Booking recorded but room count not updated.</p>";
        }
    } else {
        echo "Booking failed: " . $stmt->error;
    }
} else {
    echo "Invalid access method.";
}

$conn->close();
?>
