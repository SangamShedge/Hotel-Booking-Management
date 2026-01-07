<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Rooms.*, Hotels.name AS hotel_name FROM Rooms
        JOIN Hotels ON Rooms.hotel_id = Hotels.hotel_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rooms Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 p-6 fixed h-full">
        <div class="flex items-center space-x-3 mb-6">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
            <span class="text-2xl font-bold text-blue-600">My Stay</span>
        </div>
        <nav class="space-y-3">
            <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
            <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Profile</a>
            <a href="users.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Users</a>
            <a href="bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Bookings</a>
            <a href="hotels.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Hotels</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-blue-500 bg-blue-700">Rooms</a>
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
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">All Rooms</h1>

            <div class="text-right mb-4">
                <a href="add_room.php" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Add New Room</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto bg-gray-800 rounded-lg">
                    <thead>
                        <tr class="bg-gray-700 text-left">
                            <th class="p-3">Room ID</th>
                            <th class="p-3">Hotel</th>
                            <th class="p-3">Room Type</th>
                            <th class="p-3">Price/Night</th>
                            <th class="p-3">Max Guests</th>
                            <th class="p-3">Available</th>
                            <th class="p-3">Description</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($room = $result->fetch_assoc()): ?>
                            <tr class="border-t border-gray-700">
                                <td class="p-3"><?= $room['room_id'] ?></td>
                                <td class="p-3"><?= htmlspecialchars($room['hotel_name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($room['room_type']) ?></td>
                                <td class="p-3">₹<?= $room['price_per_night'] ?></td>
                                <td class="p-3"><?= $room['max_guests'] ?></td>
                                <td class="p-3"><?= $room['available_count'] ?></td>
                                <td class="p-3 line-clamp-2"><?= htmlspecialchars($room['description']) ?></td>
                                <td class="p-3">
                                    <a href="toggle_room_status.php?id=<?= $room['room_id'] ?>">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                                            <?= $room['status'] === 'Available' 
                                                ? 'bg-green-600 text-white hover:bg-green-700' 
                                                : 'bg-red-600 text-white hover:bg-red-700' ?>">
                                            <?= $room['status'] ?>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>

<?php $conn->close(); ?>
