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
$error = $success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_type = $_POST['room_type'];
    $price = $_POST['price_per_night'];
    $max_guests = $_POST['max_guests'];
    $available_count = $_POST['available_count'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO Rooms (hotel_id, room_type, price_per_night, max_guests, available_count, description, status) VALUES (?, ?, ?, ?, ?, ?, 'Available')");
    $stmt->bind_param("isiiis", $hotel_id, $room_type, $price, $max_guests, $available_count, $description);

    if ($stmt->execute()) {
        $success = "Room added successfully!";
    } else {
        $error = "Error adding room: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans flex items-center justify-center p-10">

    <div class="bg-gray-800 p-8 rounded-lg w-full max-w-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-blue-400 text-center">Add New Room</h2>

        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?= $error ?></p>
        <?php elseif ($success): ?>
            <p class="text-green-500 mb-4"><?= $success ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="room_type" placeholder="Room Type" class="w-full p-2 rounded bg-gray-700 text-white" required>
            <input type="number" name="price_per_night" placeholder="Price per Night" class="w-full p-2 rounded bg-gray-700 text-white" required>
            <input type="number" name="max_guests" placeholder="Max Guests" class="w-full p-2 rounded bg-gray-700 text-white" required>
            <input type="number" name="available_count" placeholder="Available Count" class="w-full p-2 rounded bg-gray-700 text-white" required>
            <textarea name="description" placeholder="Room Description" class="w-full p-2 rounded bg-gray-700 text-white" rows="4" required></textarea>

            <div class="flex justify-between items-center mt-4">
                <a href="manage_rooms.php" class="text-blue-400 hover:underline">Back to Rooms</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white">Add Room</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php $conn->close(); ?>
