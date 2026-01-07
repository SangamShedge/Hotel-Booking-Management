<?php
$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sort = $_GET['sort'] ?? '';
$locationFilter = $_GET['location'] ?? '';

$locationsResult = $conn->query("SELECT DISTINCT location FROM Hotels");
$locations = [];
while ($loc = $locationsResult->fetch_assoc()) {
    $locations[] = $loc['location'];
}

$sql = "
    SELECT h.hotel_id, h.name AS hotel_name, h.location, h.rating, h.main_image_url,
           MIN(r.price_per_night) AS min_price
    FROM Hotels h
    JOIN Rooms r ON h.hotel_id = r.hotel_id
";

if (!empty($locationFilter) && $locationFilter !== 'all') {
    $sql .= " WHERE h.location = '" . $conn->real_escape_string($locationFilter) . "'";
}

$sql .= " GROUP BY h.hotel_id";

if ($sort === 'price') {
    $sql .= " ORDER BY min_price ASC";
} elseif ($sort === 'rating') {
    $sql .= " ORDER BY h.rating DESC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hotels – My Stay</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

<header class="bg-white shadow fixed top-0 left-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-3">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
      <span class="text-2xl font-bold text-blue-600">My Stay</span>
    </div>
    <nav class="space-x-6 hidden md:flex">
      <a href="index.php" class="hover:text-blue-600 transition duration-300">Home</a>
      <a href="hotel.php" class="text-blue-600 font-semibold">Hotels</a>
      <a href="index.php#about" class="hover:text-blue-600 transition duration-300">About</a>
      <a href="index.php#contact" class="hover:text-blue-600 transition duration-300">Contact</a>
    </nav>
  </div>
</header>

<main class="pt-24 pb-16 max-w-7xl mx-auto px-4 md:flex space-x-8">

  <aside class="md:w-1/4 space-y-6 sticky top-24 self-start h-fit bg-white p-6 rounded-xl shadow transition-transform hover:scale-[1.02] duration-300 hover:bg-white">
    <h2 class="text-xl font-bold mb-4">Filters</h2>
    <form method="get" class="space-y-6">
      <div>
        <h3 class="font-semibold mb-2">Location</h3>
        <select name="location" onchange="this.form.submit()" class="w-full border rounded-md p-2">
          <option value="all">All</option>
          <?php foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>" <?= $loc == $locationFilter ? 'selected' : '' ?>>
              <?= htmlspecialchars($loc) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <h3 class="font-semibold mb-2">Sort By</h3>
        <select name="sort" onchange="this.form.submit()" class="w-full border rounded-md p-2">
          <option value="">Default</option>
          <option value="price" <?= $sort == 'price' ? 'selected' : '' ?>>Price (Low to High)</option>
          <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>Rating (High to Low)</option>
        </select>
      </div>
    </form>
  </aside>

  <section class="md:w-3/4 space-y-8">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden md:flex transition-transform hover:scale-[1.03] duration-300 hover:bg-white">
          <img src="../uploads/<?= $row['main_image_url'] ?>" class="w-full md:w-1/3 object-cover h-60" />
          <div class="p-6 flex flex-col justify-between w-full">
            <div>
              <h3 class="text-2xl font-bold hover:text-blue-600 transition duration-300">
                <?= htmlspecialchars($row['hotel_name']) ?>
              </h3>
              <p class="text-gray-600"><?= htmlspecialchars($row['location']) ?></p>
              <p class="text-sm text-gray-500 mt-1">Rating: <?= number_format($row['rating'], 1) ?> ★</p>
              <p class="text-blue-600 font-bold mt-2 text-lg">Rs <?= number_format($row['min_price']) ?> / night</p>
            </div>
            <div class="mt-4">
              <a href="hotel-detail.php?id=<?= $row['hotel_id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-300">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-gray-500">No hotels found for selected filters.</p>
    <?php endif; ?>
  </section>
</main>

<footer class="bg-white border-t mt-12">
  <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
    <p>&copy; 2025 My Stay. All rights reserved.</p>
    <div class="space-x-4 mt-4 md:mt-0">
      <a href="#" class="hover:underline">Privacy Policy</a>
      <a href="#" class="hover:underline">Terms of Service</a>
    </div>
  </div>
</footer>

</body>
</html>
<?php $conn->close(); ?>
