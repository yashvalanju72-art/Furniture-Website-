<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "furniture_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle quantity update
if (isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $product_id => $qty) {
        $product_id = intval($product_id);
        $qty = intval($qty);

        if ($qty <= 0) {
            unset($_SESSION['cart'][$product_id]);
            $conn->query("DELETE FROM cart_items WHERE user_id=$user_id AND product_id=$product_id");
        } else {
            $_SESSION['cart'][$product_id] = $qty;
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE quantity=?");
            $stmt->bind_param("iiii", $user_id, $product_id, $qty, $qty);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle remove
if (isset($_POST['remove_item'])) {
    $product_id = intval($_POST['remove_item']);
    unset($_SESSION['cart'][$product_id]);
    $conn->query("DELETE FROM cart_items WHERE user_id=$user_id AND product_id=$product_id");
}

// Fetch cart products
$cart_products = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(",", array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cart_products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Navbar -->
<nav class="bg-[#f5f5dc] shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">FurniFi Shop</h1>
    <div class="flex items-center space-x-4">
        <span class="text-gray-700">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <b><a href="index.html" class="text-green-600 hover:underline">Home</a></b>
        <b><a href="shop.php" class="text-green-600 hover:underline">Shop</a></b>
        <b><a href="logout.php" class="text-red-600 hover:underline">Logout</a></b>
    </div>
</nav>

<div class="container mx-auto py-10 px-4">
    <?php if (isset($message)): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-6 rounded"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2 class="text-3xl font-bold mb-6">My Cart</h2>

    <?php if (empty($cart_products)): ?>
        <p class="text-gray-600">Your cart is empty. <a href="shop.php" class="text-green-600 hover:underline">Go shopping</a>.</p>
    <?php else: ?>
        <form method="POST" id="cartForm">
            <div class="grid grid-cols-1 gap-6">
                <?php 
                $total = 0;
                foreach($cart_products as $product): 
                    $subtotal = $product['price'] * $product['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="bg-white p-5 rounded-lg shadow flex items-center space-x-6">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="w-24 h-24 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-gray-600">₹<?php echo number_format($product['price'], 2); ?></p>
                            <p class="text-gray-500">Subtotal: ₹<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <input type="number" name="quantities[<?php echo $product['id']; ?>]" 
                                   value="<?php echo $product['quantity']; ?>" min="1" class="border rounded px-2 py-1 w-20 quantity-input">
                            <button type="submit" name="remove_item" value="<?php echo $product['id']; ?>" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <span class="text-2xl font-bold">Total: ₹<?php echo number_format($total, 2); ?></span>
                <a href="checkout.php" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// Automatically submit form when quantity changes
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', () => {
        document.getElementById('cartForm').submit();
    });
});
</script>
<!-- Footer -->
<footer class="bg-[#d4c6a8] text-gray-800 py-6 mt-10">
  <div class="container mx-auto px-6 text-center text-sm">
    &copy; 2026 FurniFi Furniture. All rights reserved.
  </div>
</footer>


</body>
</html>
