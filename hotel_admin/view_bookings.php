<?php
session_start();

if (!isset($_SESSION['hotel_id'])) {
    header("Location: login.html");
    exit;
}

$hotel_id = $_SESSION['hotel_id'];

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings for rooms that belong to this hotel
$sql = "
    SELECT 
        b.booking_id,
        b.check_in_date,
        b.check_out_date,
        b.total_price,
        b.status,
        b.booking_date,
        u.name AS user_name,
        u.phone AS user_phone,
        r.room_type
    FROM Bookings b
    JOIN Users u ON b.user_id = u.user_id
    JOIN Rooms r ON b.room_id = r.room_id
    WHERE r.hotel_id = ?
    ORDER BY b.booking_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bookings</title>
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
        <a href="manage_rooms.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">Manage Rooms</a>
        <a href="view_bookings.php" class="block px-4 py-2 rounded bg-blue-700">View Bookings</a>
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
        <h1 class="text-3xl font-bold mb-6 text-center text-blue-400">Bookings for Your Hotel</h1>

        <div class="overflow-x-auto">
            <table class="w-full table-auto bg-gray-800 rounded-lg">
                <thead>
                    <tr class="bg-gray-700 text-left">
                        <th class="p-3">Booking ID</th>
                        <th class="p-3">Guest Name</th>
                        <th class="p-3">Contact</th>
                        <th class="p-3">Room Type</th>
                        <th class="p-3">Check-In</th>
                        <th class="p-3">Check-Out</th>
                        <th class="p-3">Total Price</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-700">
                            <td class="p-3"><?= $booking['booking_id'] ?></td>
                            <td class="p-3"><?= htmlspecialchars($booking['user_name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($booking['user_phone']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($booking['room_type']) ?></td>
                            <td class="p-3"><?= $booking['check_in_date'] ?></td>
                            <td class="p-3"><?= $booking['check_out_date'] ?></td>
                            <td class="p-3">₹<?= $booking['total_price'] ?></td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    <?= $booking['status'] === 'Booked'
                                        ? 'bg-blue-600 text-white'
                                        : ($booking['status'] === 'Completed' ? 'bg-green-600 text-white' : 'bg-red-600 text-white') ?>">
                                    <?= $booking['status'] ?>
                                </span>
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

<?php
$stmt->close();
$conn->close();
?>
