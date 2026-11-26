<?php
session_start();

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= DATABASE CONNECTION ================= */
$conn = new mysqli("localhost", "root", "", "furniture_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ================= FETCH USER INFO ================= */
$stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id=?");
if (!$stmt) {
    die("User Query Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ================= FETCH LATEST ORDER ================= */
$latest_order = null;

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 1");
if (!$stmt) {
    die("Orders Query Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $latest_order = $result->fetch_assoc();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-[#f5f5dc] shadow-md p-4 flex justify-between items-center sticky top-0 z-50">
    <h1 class="text-2xl font-bold text-green-600">
        <i class="fas fa-couch"></i> FurniFi Dashboard
    </h1>
    <div class="flex items-center space-x-6">
        <span class="text-gray-700">
            👋 Hello, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
        </span>
        <a href="index.html" class="text-red-600 hover:underline">Home</a>
        <a href="shop.php" class="text-green-600 hover:underline">Shop</a>
        <a href="cart.php" class="text-blue-600 hover:underline">Cart</a>
        <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto py-10 px-4">
    <h2 class="text-4xl font-bold mb-10 text-center text-gray-800">
        My Dashboard
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Profile Info -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-user text-green-600 text-2xl"></i>
                </div>
                <h3 class="ml-3 text-xl font-semibold">Profile Information</h3>
            </div>

            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Joined:</strong> <?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>

            <a href="edit_profile.php"
               class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
               Edit Profile
            </a>
        </div>

        <!-- Track Latest Order -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
            <div class="flex items-center mb-4">
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-truck text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="ml-3 text-xl font-semibold">Track Latest Order</h3>
            </div>

            <?php if ($latest_order): ?>

                <p><strong>Order ID:</strong> #<?php echo $latest_order['id']; ?></p>
                <p><strong>Total:</strong> ₹<?php echo number_format($latest_order['total'], 2); ?></p>
                <p><strong>Date:</strong>
                    <?php echo date("F j, Y, g:i a", strtotime($latest_order['created_at'])); ?>
                </p>
                <p><strong>Status:</strong>
                    <span class="text-green-600 font-bold">Processing</span>
                </p>

                <a href="receipt.php?order_id=<?php echo $latest_order['id']; ?>"
                   class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
                   View Order
                </a>

            <?php else: ?>

                <p class="text-gray-600">You haven’t placed any orders yet.</p>
                <a href="shop.php" class="text-green-600 hover:underline">
                    Start shopping
                </a>

            <?php endif; ?>
        </div>

        <!-- Past Orders -->
        <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-history text-purple-600 text-2xl"></i>
                </div>
                <h3 class="ml-3 text-xl font-semibold">Past Orders</h3>
            </div>

            <p class="text-gray-600 mb-4">
                View your complete order history and track progress.
            </p>

            <a href="order.php"
               class="bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition">
               View Past Orders
            </a>
        </div>

    </div>
</div>

<!-- Footer -->
<footer class="py-12 bg-[#a67c52] text-gray-200">
    <div class="container mx-auto px-6 text-center space-y-6">
        <div class="flex justify-center gap-8 text-2xl">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
        <p class="text-gray-300">&copy; 2026 FurniFi Furniture. All rights reserved.</p>
    </div>
</footer>

</body>
</html>