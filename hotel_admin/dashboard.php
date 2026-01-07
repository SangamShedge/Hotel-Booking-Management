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
$sql = "SELECT * FROM Hotels WHERE hotel_id = $hotel_id";
$result = $conn->query($sql);
$hotel = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-900 text-white min-h-screen font-sans">

<aside class="w-64 bg-gray-800 p-6 fixed h-full">
    <div class="flex items-center space-x-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-y-3 mt-6">
        <a href="../main/index.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Home</a>
        <a href="dashboard.php" class="block px-4 py-2 rounded bg-blue-700">Profile</a>
        <a href="manage_rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Manage Rooms</a>
        <a href="view_bookings.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">View Bookings</a>
        <a href="hotel_reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Reviews</a>
        <form action="logout.php" method="POST">
            <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </nav>
</aside>

<main class="ml-64 flex-1 p-10">
    <div class="max-w-3xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg relative">
        <h1 class="text-3xl font-semibold mb-6 text-center text-blue-400">Welcome, <?= htmlspecialchars($hotel['name']); ?></h1>

        <div class="flex flex-col items-center space-y-4 mb-6">
            <img src="<?= !empty($hotel['main_image_url']) ? '../uploads/' . $hotel['main_image_url'] : 'hotel.jpg'; ?>" alt="Hotel Image" class="w-32 h-32 rounded-full border-4 border-white shadow-md object-cover">
            <p class="text-lg"><strong>Hotel Name:</strong> <?= htmlspecialchars($hotel['name']); ?></p>
            <p class="text-lg"><strong>Location:</strong> <?= htmlspecialchars($hotel['location']); ?></p>
            <p class="text-lg"><strong>Rating:</strong> <?= htmlspecialchars($hotel['rating']); ?> ⭐</p>
            <p class="text-lg"><strong>Description:</strong> <?= nl2br(htmlspecialchars($hotel['description'])); ?></p>

            <button onclick="document.getElementById('popup').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded shadow text-white">
                Update Hotel Info
            </button>
        </div>
    </div>
</main>

<!-- Popup -->
<div id="popup" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white text-black p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Update Hotel Info</h2>
        <form action="update_hotel.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" value="<?= htmlspecialchars($hotel['name']) ?>" class="w-full p-2 border rounded" required>
            <input type="text" name="location" value="<?= htmlspecialchars($hotel['location']) ?>" class="w-full p-2 border rounded" required>
            <textarea name="description" class="w-full p-2 border rounded" rows="4" required><?= htmlspecialchars($hotel['description']) ?></textarea>
            <input type="file" name="main_image" accept="image/*" class="w-full p-2 border rounded">

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('popup').classList.add('hidden')" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
