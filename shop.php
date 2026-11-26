<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
    $stmt->execute();
    $stmt->close();

    $added_msg = "Product added to cart!";
}

/* =========================
   SEARCH FUNCTIONALITY
========================= */
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_sql = "";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $search_sql = "WHERE name LIKE '%$search_safe%' OR description LIKE '%$search_safe%'";
}

/* =========================
   PAGINATION SETUP
========================= */
$limit = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Total products
$total_result = $conn->query("SELECT COUNT(*) AS total FROM products $search_sql");
$total_products = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Fetch products
$result = $conn->query("SELECT * FROM products $search_sql ORDER BY id ASC LIMIT $limit OFFSET $offset");

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['image'] = 'img/products/' . basename($row['image']);
    $products[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f0e1] min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="bg-[#d8c3a5] shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">FurniFi Shop</h1>

    <div class="flex items-center space-x-4">

        <!-- SEARCH BAR -->
        <form method="GET" class="flex items-center bg-white rounded overflow-hidden shadow">
            <input type="text" name="search"
                   value="<?php echo htmlspecialchars($search); ?>"
                   placeholder="Search products..."
                   class="px-3 py-1 outline-none w-44">
            <button type="submit"
                    class="bg-green-600 px-3 py-1 text-white hover:bg-green-700">
                🔍
            </button>
        </form>

        <span class="text-gray-700">
            Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>

        <a href="index.html" class="text-red-600 hover:underline font-bold">Home</a>
        <a href="dashboard.php" class="text-green-600 hover:underline font-bold">Dashboard</a>

        <a href="cart.php" class="text-blue-600 hover:underline font-bold">
            🛒(<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)
        </a>

        <a href="logout.php" class="text-red-600 hover:underline font-bold">Logout</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mx-auto py-10 px-4 flex-1">

    <?php if (isset($added_msg)): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-6 rounded">
            <?php echo $added_msg; ?>
        </div>
    <?php endif; ?>

    <h2 class="text-3xl font-bold mb-6">Our Products</h2>

    <?php if (empty($products)): ?>
        <p class="text-center text-gray-600 text-lg">No products found.</p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

        <?php foreach($products as $product): ?>
        <div class="bg-[#fffaf0] rounded-lg shadow-lg overflow-hidden flex flex-col border border-[#d4c6a8]">

            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 class="w-full h-64 object-cover">

            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                    <p class="text-gray-600 mb-2">
                        ₹<?php echo number_format($product['price'], 2); ?>
                    </p>

                    <p class="text-gray-500 text-sm mb-2">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                </div>

                <form method="POST" class="flex space-x-2">
                    <input type="hidden" name="product_id"
                           value="<?php echo $product['id']; ?>">

                    <input type="number" name="quantity"
                           value="1" min="1"
                           class="w-16 border rounded px-2">

                    <button type="submit" name="add_to_cart"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- PAGINATION -->
    <div class="flex justify-center mt-10 space-x-3">

        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
               Previous
            </a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"
               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
               Next
            </a>
        <?php endif; ?>

    </div>
</div>

<!-- FOOTER -->
<footer class="bg-[#a67c52] text-gray-200 py-12 mt-auto">
    <div class="container mx-auto px-6 text-center space-y-6">
        <p>&copy; 2026 FurniFi Furniture. All rights reserved.</p>
    </div>
</footer>

</body>
</html>