<?php
session_start();

// Allow only logged-in hotel admins
if (!isset($_SESSION['hotel_id'])) {
    header("Location: login.html");
    exit;
}

$hotel_id = $_SESSION['hotel_id'];

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch rooms for this hotel only
$sql = "SELECT * FROM Rooms WHERE hotel_id = $hotel_id";
$result = $conn->query($sql);

// Fetch hotel name for display
$hotel_sql = "SELECT name FROM Hotels WHERE hotel_id = $hotel_id";
$hotel_res = $conn->query($hotel_sql);
$hotel_name = $hotel_res->fetch_assoc()['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
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
            <a href="manage_rooms.php" class="block px-4 py-2 rounded bg-blue-700">Manage Rooms</a>
            <a href="view_bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">View Bookings</a>
            <a href="hotel_reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Reviews</a>
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
            <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">Rooms - <?= htmlspecialchars($hotel_name) ?></h1>

            <div class="text-right mb-4">
                <a href="add_room.php" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Add New Room</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-auto bg-gray-800 rounded-lg">
                    <thead>
                        <tr class="bg-gray-700 text-left">
                            <th class="p-3">Room ID</th>
                            <th class="p-3">Room Type</th>
                            <th class="p-3">Price/Night</th>
                            <th class="p-3">Max Guests</th>
                            <th class="p-3">Available</th>
                            <th class="p-3">Description</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($room = $result->fetch_assoc()): ?>
                            <tr class="border-t border-gray-700">
                                <td class="p-3"><?= $room['room_id'] ?></td>
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
                                <td class="p-3 space-x-2">
                                    <button onclick='openEditModal(<?= json_encode($room) ?>)' class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-sm">Edit</button>
                                    <a href="delete_room.php?id=<?= $room['room_id'] ?>" onclick="return confirm('Are you sure you want to delete this room?')" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white text-black p-6 rounded-lg w-full max-w-md relative">
        <h2 class="text-xl font-bold mb-4">Edit Room</h2>
        <form id="editForm" method="POST" action="update_room.php" class="space-y-4">
            <input type="hidden" name="room_id" id="edit_room_id">
            <input type="text" name="room_type" id="edit_room_type" class="w-full p-2 border rounded" required>
            <input type="number" name="price_per_night" id="edit_price" class="w-full p-2 border rounded" required>
            <input type="number" name="max_guests" id="edit_max_guests" class="w-full p-2 border rounded" required>
            <input type="number" name="available_count" id="edit_available" class="w-full p-2 border rounded" required>
            <textarea name="description" id="edit_description" class="w-full p-2 border rounded" required></textarea>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(room) {
    document.getElementById('edit_room_id').value = room.room_id;
    document.getElementById('edit_room_type').value = room.room_type;
    document.getElementById('edit_price').value = room.price_per_night;
    document.getElementById('edit_max_guests').value = room.max_guests;
    document.getElementById('edit_available').value = room.available_count;
    document.getElementById('edit_description').value = room.description;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

</body>
</html>

<?php $conn->close(); ?>
