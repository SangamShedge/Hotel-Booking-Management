<?php
session_start();

$conn = new mysqli("localhost", "root", "", "hotelbooking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT name, profile_picture FROM Users WHERE user_id = $user_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HotelBooking - Find Your Stay</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .hover-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      background:rgb(127, 186, 115);
    }
    .review-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      background:rgb(115, 171, 186);
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Meetup_Logo.png" alt="Logo" class="w-10 h-10">
        <span class="text-2xl font-bold text-blue-600">My Stay</span>
      </div>
      <nav class="space-x-6 hidden md:flex">
        <a href="index.php" class="hover:text-blue-600">Home</a>
        <a href="hotel.php" class="hover:text-blue-600">Hotels</a>
        <a href="#about" class="hover:text-blue-600">About</a>
        <a href="#contact" class="hover:text-blue-600">Contact</a>
      </nav>

      <div class="hidden md:flex space-x-4 items-center">
        <?php
          $isHotelAdmin = isset($_SESSION['hotel_id']);
          $isUser = isset($_SESSION['user_id']);
        ?>

        <?php if ($isHotelAdmin): ?>
          <?php
            $hotel_id = $_SESSION['hotel_id'];
            $hotel_sql = "SELECT name, main_image_url FROM Hotels WHERE hotel_id = $hotel_id";
            $hotel_result = $conn->query($hotel_sql);
            $hotel = $hotel_result->fetch_assoc();
          ?>
          <a href="../hotel_admin/dashboard.php" class="flex items-center space-x-2">
            <img src="<?php echo !empty($hotel['main_image_url']) ? '../uploads/' . $hotel['main_image_url'] : 'hotel.jpg'; ?>" alt="Hotel" class="w-10 h-10 rounded-full border border-blue-500 object-cover">
            <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($hotel['name']); ?></span>
          </a>

        <?php elseif ($isUser): ?>
          <?php
            $dashboardPage = ($user_id == 1) ? "../admin/dashboard.php" : "../user/dashboard.php";
          ?>
          <a href="<?php echo $dashboardPage; ?>" class="flex items-center space-x-2">
            <img src="<?php echo $user['profile_picture'] ?: 'user.jpeg'; ?>" alt="Profile" class="w-10 h-10 rounded-full border border-blue-500">
            <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($user['name']); ?></span>
          </a>

        <?php else: ?>
          <a href="../login_signup/login.html">
            <button class="px-4 py-2 border rounded-lg text-blue-600 border-blue-600 hover:bg-blue-50">Login</button>
          </a>
          <a href="../login_signup/signup.html">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Sign Up</button>
          </a>
        <?php endif; ?>
      </div>


    </div>
  </header>

  <!-- Hero Carousel Section -->
  <section class="relative h-screen overflow-hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
    <img id="carouselImage" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 z-0" src="https://c4.wallpaperflare.com/wallpaper/451/186/556/best-hotels-booking-pool-vacation-wallpaper-preview.jpg" />
    <div class="relative z-20 flex flex-col items-center justify-center h-full text-center text-white px-4">
      <h1 class="text-5xl md:text-6xl font-extrabold animate__animated animate__fadeInDown bg-gradient-to-r from-blue-400 via-purple-500 to-indigo-600 bg-clip-text text-transparent">
        Find Your Perfect Hotel
      </h1>
      <p class="mt-4 text-xl md:text-2xl animate__animated animate__fadeInUp">Search and stay in the most beautiful places in the world</p>
      <a href="hotel.php" class="mt-8 inline-block px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg shadow-lg hover:bg-blue-100 transition duration-300">Explore Now</a>
    </div>
  </section>

  <!-- Featured Hotels -->
  <section id="hotels" class="max-w-7xl mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center mb-10 animate__animated animate__fadeIn">Featured Hotels</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <!-- Hotel Card -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform hover:scale-[1.05] duration-300 hover:bg-white">
        <img src="https://c4.wallpaperflare.com/wallpaper/451/186/556/best-hotels-booking-pool-vacation-wallpaper-preview.jpg" alt="Hotel" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Sunset Resort</h3>
          <p class="text-sm text-gray-600 mb-2">Goa, India</p>
          <p class="font-bold text-blue-600">Rs 12000 / night</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform hover:scale-[1.05] duration-300 hover:bg-white">
        <img src="https://wallpapercave.com/wp/wp12814684.jpg" alt="Hotel" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Beach Paradise</h3>
          <p class="text-sm text-gray-600 mb-2">Maldives</p>
          <p class="font-bold text-blue-600">Rs 24000 / night</p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform hover:scale-[1.05] duration-300 hover:bg-white">
        <img src="https://media.istockphoto.com/id/146753772/photo/resort-in-cancun-shown-in-the-daytime-from-the-air.jpg?s=612x612&w=0&k=20&c=TsDt2nU2cZBsQ4H4czSyTXWpHtcWcJWTrfsjq4ahSb8=" alt="Hotel" class="w-full h-48 object-cover">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Urban Stay</h3>
          <p class="text-sm text-gray-600 mb-2">Mumbai</p>
          <p class="font-bold text-blue-600">Rs 10000 / night</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Top Destinations -->
  <section class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-10">Top Destinations</h2>
      <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl overflow-hidden shadow transition-transform hover:scale-[1.05] duration-300">
          <img src="https://s7ap1.scene7.com/is/image/incredibleindia/hawa-mahal-jaipur-rajasthan-city-1-hero?qlt=82&ts=1726660605161" class="w-full h-40 object-cover">
          <div class="p-4">Jaipur, Rajstan</div>
        </div>
        <div class="bg-white rounded-xl overflow-hidden shadow transition-transform hover:scale-[1.05] duration-300">
          <img src="https://www.the-world.in/wp-content/uploads/2024/04/The-World-Blog-Charms-of-Surat-Landscape.webp" class="w-full h-40 object-cover">
          <div class="p-4">Surat, Gujrat</div>
        </div>
        <div class="bg-white rounded-xl overflow-hidden shadow transition-transform hover:scale-[1.05] duration-300">
          <img src="https://clubmahindra.gumlet.io/blog/media/section_images/shuttersto-08e37755538c941.jpg?w=376&dpr=2.6" class="w-full h-40 object-cover">
          <div class="p-4">Mahableshwar, Maharashtra</div>
        </div>
        <div class="bg-white rounded-xl overflow-hidden shadow transition-transform hover:scale-[1.05] duration-300">
          <img src="https://boutindia.s3.us-east-2.amazonaws.com/images/blog/images/2023-07-21-13-58-37-64ba41b586366-Agra-The-Glimpse-of-Mughal-Kingdom.jpg" class="w-full h-40 object-cover">
          <div class="p-4">Agra, Delhi</div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us Section -->
  <section id="about" class="bg-white py-16">
    <div class="max-w-5xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-6">About Us</h2>
      <p class="text-gray-600 text-lg leading-relaxed">
        My Stay is your trusted partner in finding the best hotels around the world. Whether you're looking for a beachside resort or a city hotel, we help you discover, compare, and book with ease. Our mission is to make travel more enjoyable and convenient for everyone.
      </p>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-6">Why Choose Us?</h2>
      <div class="grid md:grid-cols-3 gap-8 text-left mt-10">
        <div class="hover-card bg-blue-50 p-6 rounded-xl shadow-md transform transition duration-300">
          <h3 class="text-xl font-semibold mb-2">Best Price Guarantee</h3>
          <p>We ensure you always get the best rates and deals available.</p>
        </div>
        <div class="hover-card bg-blue-50 p-6 rounded-xl shadow-md transform transition duration-300">
          <h3 class="text-xl font-semibold mb-2">Verified Reviews</h3>
          <p>Read real guest reviews and ratings before booking your stay.</p>
        </div>
        <div class="hover-card bg-blue-50 p-6 rounded-xl shadow-md transform transition duration-300">
          <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
          <p>Our team is available anytime to assist you during your travel.</p>
        </div>
      </div>
    </div>
  </section>

    <!-- Testimonials -->
  <section class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-10">What Our Customers Say</h2>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="review-card bg-gray-100 p-6 rounded-xl shadow transform transition duration-300">
          <p class="mb-4">“I found the best hotel in seconds. Super easy to use!”</p>
          <h4 class="font-semibold">– Sravan S.</h4>
        </div>
        <div class="review-card bg-gray-100 p-6 rounded-xl shadow transform transition duration-300">
          <p class="mb-4">“The rates were better than any other platform. Loved it!”</p>
          <h4 class="font-semibold">– Ramesh K.</h4>
        </div>
        <div class="review-card bg-gray-100 p-6 rounded-xl shadow transform transition duration-300">
          <p class="mb-4">“Customer support was helpful when I needed to change dates.”</p>
          <h4 class="font-semibold">– Ayush M.</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="bg-blue-50 py-16">
  <div class="max-w-4xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-8">Contact Us</h2>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alertLogin();</script>";
            exit;
        }

        $conn = new mysqli("localhost", "root", "", "hotelbooking");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $user_id = $_SESSION['user_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $message = $conn->real_escape_string($_POST['message']);

        $sql = "INSERT INTO ContactMessages (user_id, name, email, message) 
                VALUES ('$user_id', '$name', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo "<p class='text-green-600 text-center mb-4'>Message sent successfully!</p>";
        } else {
            echo "<p class='text-red-600 text-center mb-4'>Error: " . $conn->error . "</p>";
        }

        $conn->close();
    }
    ?>

      <form class="space-y-6" method="POST" action="">
        <div class="grid md:grid-cols-2 gap-4">
          <input type="text" name="name" required placeholder="Your Name" class="w-full px-4 py-2 border rounded-md" />
          <input type="email" name="email" required placeholder="Your Email" class="w-full px-4 py-2 border rounded-md" />
        </div>
        <textarea name="message" required placeholder="Your Message" rows="5" class="w-full px-4 py-2 border rounded-md"></textarea>
        <div class="text-center">
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Send Message</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-t mt-12">
    <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
      <p class="animate__animated animate__fadeInUp">&copy; 2025 My Stay. All rights reserved.</p>
      <div class="space-x-4 mt-4 md:mt-0 animate__animated animate__fadeInUp">
        <a href="#" class="hover:underline">Privacy Policy</a>
        <a href="#" class="hover:underline">Terms of Service</a>
      </div>
    </div>
  </footer>

  <!-- Simple Carousel Script -->
  <script>
    const images = [
      "https://c4.wallpaperflare.com/wallpaper/451/186/556/best-hotels-booking-pool-vacation-wallpaper-preview.jpg",
      "https://c4.wallpaperflare.com/wallpaper/921/708/937/best-hotels-travel-thailand-tourism-wallpaper-preview.jpg",
      "https://wallpapershome.com/images/pages/ico_h/659.jpg",
    ];
    let current = 0;
    const carousel = document.getElementById("carouselImage");

    setInterval(() => {
      current = (current + 1) % images.length;
      carousel.src = images[current];
    }, 4000);
  </script>

</body>
</html>
