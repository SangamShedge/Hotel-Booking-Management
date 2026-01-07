<?php
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$hotelQuery = $conn->prepare("SELECT * FROM Hotels WHERE hotel_id = ?");
$hotelQuery->bind_param("i", $hotel_id);
$hotelQuery->execute();
$hotelResult = $hotelQuery->get_result();

if ($hotelResult->num_rows === 0) {
    echo "Hotel not found.";
    exit;
}

$hotel = $hotelResult->fetch_assoc();

$roomsQuery = $conn->prepare("SELECT * FROM Rooms WHERE hotel_id = ?");
$roomsQuery->bind_param("i", $hotel_id);
$roomsQuery->execute();
$roomsResult = $roomsQuery->get_result();

$availableRooms = [];
$unavailableRooms = [];

while ($room = $roomsResult->fetch_assoc()) {
    if ($room['status'] === 'Available') {
        $availableRooms[] = $room;
    } else {
        $unavailableRooms[] = $room;
    }
}

$reviewsQuery = $conn->prepare("
    SELECT R.rating, R.comment, R.created_at, U.name 
    FROM Reviews R 
    JOIN Users U ON R.user_id = U.user_id 
    WHERE R.hotel_id = ?
    ORDER BY R.created_at DESC
");
$reviewsQuery->bind_param("i", $hotel_id);
$reviewsQuery->execute();
$reviewsResult = $reviewsQuery->get_result();
$reviews = $reviewsResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($hotel['name']) ?> – Hotel Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-white shadow fixed top-0 left-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-3">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
      <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-x-6 hidden md:flex">
      <a href="index.php" class="hover:text-blue-600">Home</a>
      <a href="hotel.php" class="hover:text-blue-600">Hotels</a>
      <a href="index.php#about" class="hover:text-blue-600">About</a>
      <a href="index.php#contact" class="hover:text-blue-600">Contact</a>
    </nav>
  </div>
</header>

<main class="pt-24 max-w-5xl mx-auto px-4 pb-12">
  <section class="bg-white shadow rounded-xl p-6 mb-8 transition-transform hover:scale-[1.02] duration-300">
    <img src="../uploads/<?= $hotel['main_image_url'] ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" class="w-full rounded-md h-64 object-cover mb-6">
    <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($hotel['name']) ?></h1>
    <p class="text-gray-600 mb-1">Location: <?= htmlspecialchars($hotel['location']) ?></p>
    <p class="text-yellow-500 font-medium mb-2">Rating: <?= number_format($hotel['rating'], 1) ?> ★</p>
    <p class="text-gray-700"><?= nl2br(htmlspecialchars($hotel['description'])) ?></p>

    <?php if (!empty($reviews)): ?>
      <button onclick="toggleModal()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition duration-300">
        View Reviews
      </button>
    <?php endif; ?>
  </section>

  <section>
    <h2 class="text-2xl font-bold mb-4">Available Rooms</h2>
    <?php if (count($availableRooms) > 0): ?>
      <div class="space-y-6">
        <?php foreach ($availableRooms as $room): ?>
          <div class="bg-white shadow rounded-lg p-5 transition-transform hover:scale-[1.01] duration-300">
            <h3 class="text-xl font-semibold"><?= htmlspecialchars($room['room_type']) ?></h3>
            <p class="text-gray-600 mt-1 mb-2">Max Guests: <?= $room['max_guests'] ?></p>
            <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($room['description'])) ?></p>
            <p class="text-blue-600 font-bold text-lg mb-2">Rs <?= number_format($room['price_per_night']) ?> / night</p>
            <p class="text-sm text-gray-500">Available: <?= $room['available_count'] ?></p>

            <button type="button" onclick="toggleDrawer('drawer-<?= $room['room_id'] ?>')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
              Book Now
            </button>

            <div id="drawer-<?= $room['room_id'] ?>" class="hidden mt-4">
              <form action="book.php" method="POST" class="space-y-2">
                <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                <label class="block text-sm font-medium">Check-in Date:
                  <input type="date" name="check_in_date" required class="mt-1 block w-full border border-gray-300 rounded p-1">
                </label>
                <label class="block text-sm font-medium">Check-out Date:
                  <input type="date" name="check_out_date" required class="mt-1 block w-full border border-gray-300 rounded p-1">
                </label>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300">
                  Confirm Booking
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">No available rooms at this time.</p>
    <?php endif; ?>
  </section>

  <?php if (count($unavailableRooms) > 0): ?>
    <section class="mt-12">
      <h2 class="text-2xl font-bold mb-4 text-red-600">Unavailable Rooms</h2>
      <div class="space-y-6">
        <?php foreach ($unavailableRooms as $room): ?>
          <div class="bg-white shadow rounded-lg p-5 opacity-70">
            <h3 class="text-xl font-semibold"><?= htmlspecialchars($room['room_type']) ?></h3>
            <p class="text-gray-600 mt-1 mb-2">Max Guests: <?= $room['max_guests'] ?></p>
            <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($room['description'])) ?></p>
            <p class="text-gray-500 font-bold text-lg mb-2">Rs <?= number_format($room['price_per_night']) ?> / night</p>
            <p class="text-sm text-gray-400 italic">Currently unavailable for booking</p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative overflow-y-auto max-h-[80vh]">
    <button onclick="toggleModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
    <h2 class="text-2xl font-bold mb-4 text-center text-indigo-600">Hotel Reviews</h2>

    <?php if (empty($reviews)): ?>
      <p class="text-gray-600">No reviews yet.</p>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($reviews as $review): ?>
          <div class="bg-gray-100 p-4 rounded shadow transition-transform hover:scale-[1.02] duration-300">
            <div class="flex justify-between items-center mb-1">
              <span class="font-semibold"><?= htmlspecialchars($review['name']) ?></span>
              <span class="text-yellow-500"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></span>
            </div>
            <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
            <p class="text-xs text-gray-500 mt-2">Posted on <?= date('M d, Y', strtotime($review['created_at'])) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="bg-white border-t mt-12">
  <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
    <p>&copy; 2025 My Stay. All rights reserved.</p>
    <div class="space-x-4 mt-4 md:mt-0">
      <a href="#" class="hover:underline">Privacy Policy</a>
      <a href="#" class="hover:underline">Terms of Service</a>
    </div>
  </div>
</footer>

<script>
function toggleModal() {
  const modal = document.getElementById('reviewModal');
  modal.classList.toggle('hidden');
  modal.classList.toggle('flex');
}

function toggleDrawer(id) {
  const el = document.getElementById(id);
  el.classList.toggle('hidden');
}
</script>
</body>
</html>
<?php $conn->close(); ?>
