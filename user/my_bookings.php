<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Automatically mark bookings as 'Completed' if check_out_date has passed
$updateCompleted = $conn->prepare("
    UPDATE Bookings 
    SET status = 'Completed' 
    WHERE user_id = ? 
      AND status = 'Booked' 
      AND check_out_date < CURDATE()
");
$updateCompleted->bind_param("i", $user_id);
$updateCompleted->execute();

// Handle cancel booking request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_booking_id'])) {
    $booking_id = (int) $_POST['cancel_booking_id'];

    $getRoom = $conn->prepare("SELECT room_id FROM Bookings WHERE booking_id = ? AND user_id = ? AND status = 'Booked'");
    $getRoom->bind_param("ii", $booking_id, $user_id);
    $getRoom->execute();
    $roomResult = $getRoom->get_result();

    if ($roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();
        $room_id = $room['room_id'];

        $stmt = $conn->prepare("UPDATE Bookings SET status = 'Cancelled' WHERE booking_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $booking_id, $user_id);
        if ($stmt->execute()) {
            $updateRoom = $conn->prepare("
                UPDATE Rooms 
                SET available_count = available_count + 1, 
                    status = CASE WHEN available_count + 1 > 0 THEN 'Available' ELSE status END 
                WHERE room_id = ?
            ");
            $updateRoom->bind_param("i", $room_id);
            $updateRoom->execute();
        }
    }
}

// Fetch user bookings
$query = "
SELECT B.*, R.room_type, R.description AS room_description, H.name AS hotel_name, H.location
FROM Bookings B
JOIN Rooms R ON B.room_id = R.room_id
JOIN Hotels H ON R.hotel_id = H.hotel_id
WHERE B.user_id = ?
ORDER BY B.booking_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function confirmCancel(bookingId) {
        if (confirm("Are you sure you want to cancel this booking?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cancel_booking_id';
            input.value = bookingId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
  </script>
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
    <a href="my_booking.php" class="block px-4 py-2 rounded bg-blue-700">My Bookings</a>
    <a href="my_reviews.php" class="block px-4 py-2 rounded hover:bg-blue-500 transition">My Reviews</a>
    <form action="logout.php" method="POST">
      <button type="submit" class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</button>
    </form>
  </nav>
</aside>

<!-- Main Content -->
<main class="ml-64 flex-1 p-10">
  <div class="max-w-4xl mx-auto bg-gray-800 p-8 rounded-lg shadow-lg">
    <h1 class="text-3xl font-semibold mb-6 text-center text-blue-400">My Bookings</h1>

    <?php if ($result->num_rows > 0): ?>
      <div class="space-y-6">
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $isBooked = $row['status'] === 'Booked';
            $checkOutDate = strtotime($row['check_out_date']);
            $today = strtotime(date("Y-m-d"));
          ?>
          <div class="bg-gray-700 p-6 rounded-lg shadow relative">
            <h2 class="text-xl font-bold text-blue-300"><?= htmlspecialchars($row['hotel_name']) ?> – <?= htmlspecialchars($row['room_type']) ?></h2>
            <p class="text-gray-300">Location: <?= htmlspecialchars($row['location']) ?></p>
            <p class="mt-2 text-gray-200"><?= nl2br(htmlspecialchars($row['room_description'])) ?></p>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-300">
              <div><strong>Check-in:</strong> <?= $row['check_in_date'] ?></div>
              <div><strong>Check-out:</strong> <?= $row['check_out_date'] ?></div>
              <div><strong>Total:</strong> ₹<?= number_format($row['total_price'], 2) ?></div>
              <div><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></div>
            </div>

            <p class="mt-2 text-xs text-gray-500">Booked on <?= date('d M Y, h:i A', strtotime($row['booking_date'])) ?></p>

            <?php if ($isBooked && $checkOutDate > $today): ?>
              <button onclick="confirmCancel(<?= $row['booking_id'] ?>)"
                      class="absolute top-4 right-4 bg-red-600 hover:bg-red-700 px-4 py-1 text-sm rounded">
                Cancel
              </button>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-400 text-center mt-6">You have no bookings yet. <a href="hotels.php" class="text-blue-400 hover:underline">Browse hotels</a>.</p>
    <?php endif; ?>
  </div>
</main>

</body>
</html>

<?php $conn->close(); ?>
