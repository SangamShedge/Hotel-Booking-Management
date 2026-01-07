<?php
session_start();

// Admin access check
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.html");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings with user and room info
$sql = "SELECT 
            b.booking_id, b.check_in_date, b.check_out_date, b.total_price, 
            b.booking_date, b.status,
            u.name AS user_name, u.email,
            r.room_id, r.room_type
        FROM Bookings b
        JOIN Users u ON b.user_id = u.user_id
        JOIN Rooms r ON b.room_id = r.room_id
        ORDER BY b.booking_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
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
            <a href="#" class="block px-4 py-2 rounded bg-blue-700">Bookings</a>
            <a href="hotels.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Hotels</a>
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
        <div class="max-w-6xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
            <h1 class="text-3xl font-semibold mb-6 text-center">All Bookings</h1>

            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="p-3 text-left">Booking ID</th>
                            <th class="p-3 text-left">User</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-left">Room Type</th>
                            <th class="p-3 text-left">Check-In</th>
                            <th class="p-3 text-left">Check-Out</th>
                            <th class="p-3 text-left">Total Price</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-700 text-sm">
                            <td class="p-3"><?php echo $row['booking_id']; ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['room_type']); ?></td>
                            <td class="p-3"><?php echo $row['check_in_date']; ?></td>
                            <td class="p-3"><?php echo $row['check_out_date']; ?></td>
                            <td class="p-3">₹<?php echo number_format($row['total_price'], 2); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['status']); ?></td>
                            <td class="p-3 text-gray-400"><?php echo $row['booking_date']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>

<?php $conn->close(); ?>
