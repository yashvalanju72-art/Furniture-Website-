<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch user orders
$orders = [];
$sql_orders = "SELECT * FROM orders WHERE user_id=? ORDER BY id DESC";
$stmt = $conn->prepare($sql_orders);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orders[$row['id']] = $row;
    $orders[$row['id']]['items'] = [];
}
$stmt->close();

// Fetch order items
if (!empty($orders)) {
    $order_ids = implode(",", array_keys($orders));
    $sql_items = "
        SELECT oi.*, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($order_ids)
    ";
    $result = $conn->query($sql_items);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($orders[$row['order_id']])) {
                $orders[$row['order_id']]['items'][] = $row;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
function toggleItems(orderId) {
    let section = document.getElementById("items-" + orderId);
    section.classList.toggle("hidden");
}
</script>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Navbar -->
<nav class="bg-[#f5f5dc] shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">FurniFi Shop</h1>
    <div class="flex items-center space-x-4">
        <span class="text-gray-700">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="index.html" class="text-red-600 hover:underline">Home</a>
        <a href="shop.php" class="text-green-600 hover:underline">Shop</a>
        <a href="cart.php" class="text-blue-600 hover:underline">Cart</a>
        <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
    </div>
</nav>

<div class="container mx-auto py-10 px-4">
    <h2 class="text-3xl font-bold mb-6">My Orders</h2>

    <?php if (empty($orders)): ?>
        <p class="text-gray-600">You have not placed any orders yet. 
            <a href="shop.php" class="text-green-600 hover:underline">Start shopping</a>.
        </p>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold">Order #<?php echo $order['id']; ?></h3>
                            <p class="text-gray-600">Placed on: 
                                <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?>
                            </p>
                            <p class="font-bold text-lg mt-1">Total: ₹<?php echo number_format($order['total'], 2); ?></p>
                        </div>
                        <button onclick="toggleItems(<?php echo $order['id']; ?>)" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            View Items
                        </button>
                    </div>

                    <div id="items-<?php echo $order['id']; ?>" class="hidden mt-4 space-y-4">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                     class="w-20 h-20 object-cover rounded">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="text-gray-600">Subtotal: ₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="py-20 bg-[#a67c52] text-gray-200 py-12">
    <div class="container mx-auto px-6 text-center space-y-6">
      <div class="flex justify-center gap-8 text-2xl">
        <a href="#" class="hover:text-green-500 transition"><i class="fab fa-facebook"></i></a>
        <a href="#" class="hover:text-green-500 transition"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-green-500 transition"><i class="fab fa-instagram"></i></a>
      </div>
      <p class="text-gray-400">&copy; 2026 FurniFi Furniture. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
