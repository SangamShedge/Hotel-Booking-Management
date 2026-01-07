<?php
session_start();

// Only admin (user_id = 1) allowed
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hotels for dropdown
$hotels = $conn->query("SELECT hotel_id, name FROM Hotels");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_id = $_POST['hotel_id'];
    $room_type = $_POST['room_type'];
    $price_per_night = $_POST['price_per_night'];
    $max_guests = $_POST['max_guests'];
    $description = $_POST['description'];
    $available_count = $_POST['available_count'];

    $stmt = $conn->prepare("INSERT INTO Rooms (hotel_id, room_type, price_per_night, max_guests, description, available_count)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdisi", $hotel_id, $room_type, $price_per_night, $max_guests, $description, $available_count);

    if ($stmt->execute()) {
        header("Location: rooms.php");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 p-6 fixed h-full">
        <div class="flex items-center space-x-3">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
            <span class="text-2xl font-bold text-blue-600">My Stay</span>
        </div>
        <nav class="space-y-3 mt-6">
            <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
            <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Profile</a>
            <a href="users.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Users</a>
            <a href="bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Bookings</a>
            <a href="hotels.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Hotels</a>
            <a href="rooms.php" class="block px-4 py-2 rounded bg-blue-700">Rooms</a>
            <a href="reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Reviews</a>
            <a href="messages.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Messages</a>
            <form action="logout.php" method="POST">
                <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-10">
        <div class="max-w-2xl mx-auto bg-gray-800 p-8 rounded shadow-lg">
            <h2 class="text-3xl font-bold mb-6 text-center text-blue-400">Add New Room</h2>

            <?php if (!empty($error)): ?>
                <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-1">Select Hotel</label>
                    <select name="hotel_id" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                        <option value="" disabled selected>Select Hotel</option>
                        <?php while ($row = $hotels->fetch_assoc()): ?>
                            <option value="<?= $row['hotel_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Room Type</label>
                    <input type="text" name="room_type" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Price per Night</label>
                    <input type="number" name="price_per_night" step="0.01" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Max Guests</label>
                    <input type="number" name="max_guests" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Available Count</label>
                    <input type="number" name="available_count" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow w-full">
                    Add Room
                </button>
            </form>
        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>
