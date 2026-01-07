<?php
session_start();

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hotel data
$sql = "SELECT * FROM Hotels ORDER BY rating DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotels</title>
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
            <a href="#" class="block px-4 py-2 rounded bg-blue-700">Hotels</a>
            <a href="rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Rooms</a>
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
        <div class="max-w-7xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-semibold">Hotel Listings</h1>
                <a href="add_hotel.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                    + Add Hotel
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($hotel = $result->fetch_assoc()) {
                    $image = !empty($hotel['main_image_url']) ? '../uploads/' . $hotel['main_image_url'] : '../uploads/hotel_img.jpg';
                ?>
                <div class="bg-gray-700 rounded-lg overflow-hidden shadow-md relative group">
                    <!-- Three-dot Menu Button -->
                    <div class="absolute top-2 right-2">
                        <button onclick="toggleDropdown(<?php echo $hotel['hotel_id']; ?>)" class="text-white focus:outline-none">
                            <svg class="w-6 h-6 fill-current text-gray-300 hover:text-white" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="2"/>
                                <circle cx="12" cy="12" r="2"/>
                                <circle cx="19" cy="12" r="2"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown-<?php echo $hotel['hotel_id']; ?>" class="hidden absolute right-0 mt-2 w-32 bg-gray-800 border border-gray-700 rounded shadow-lg z-10">
                            <a href="edit_hotel.php?id=<?php echo $hotel['hotel_id']; ?>" class="block px-4 py-2 text-sm hover:bg-gray-700">Edit</a>
                            <a href="delete_hotel.php?id=<?php echo $hotel['hotel_id']; ?>" onclick="return confirm('Are you sure you want to delete this hotel?');" class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-700">Delete</a>
                        </div>
                    </div>

                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Hotel Image" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($hotel['name']); ?></h2>
                        <p class="text-sm text-gray-300 mb-2"><?php echo htmlspecialchars($hotel['location']); ?></p>
                        <p class="text-sm text-gray-400 mb-2"><?php echo htmlspecialchars($hotel['description']); ?></p>
                        <p class="text-yellow-400 font-semibold">Rating: <?php echo number_format($hotel['rating'], 1); ?>/5</p>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById('dropdown-' + id);
        const allDropdowns = document.querySelectorAll("[id^='dropdown-']");
        allDropdowns.forEach(el => {
            if (el !== dropdown) el.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    // Hide dropdowns on outside click
    document.addEventListener('click', function(event) {
        const isDropdownButton = event.target.closest("button[onclick^='toggleDropdown']");
        if (!isDropdownButton) {
            document.querySelectorAll("[id^='dropdown-']").forEach(el => el.classList.add('hidden'));
        }
    });
    </script>

</body>
</html>

<?php $conn->close(); ?>
