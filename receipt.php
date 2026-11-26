<?php
session_start();

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

/* ================= DATABASE ================= */
$conn = new mysqli("localhost", "root", "", "furniture_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ================= FETCH ORDER ================= */
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
if (!$stmt) {
    die("Order query error: " . $conn->error);
}

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found or access denied.");
}

/* ================= FETCH ORDER ITEMS ================= */
$order_items = [];

$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name AS product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");

if ($stmt) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $order_items[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt | FurniFi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="container mx-auto py-10 px-4 max-w-4xl">
    <div class="bg-white p-8 rounded-xl shadow-lg">

        <h2 class="text-3xl font-bold mb-6 text-center text-green-600">
            🧾 Order Receipt
        </h2>

        <!-- ORDER INFO -->
        <div class="mb-6 grid grid-cols-2 gap-4 text-lg">
            <p><strong>Order ID:</strong> #<?= $order['id']; ?></p>
            <p><strong>Date:</strong>
                <?= isset($order['created_at']) 
                    ? date("F j, Y, g:i a", strtotime($order['created_at'])) 
                    : "N/A"; ?>
            </p>
            <p><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Total:</strong> ₹<?= number_format($order['total'], 2); ?></p>
        </div>

        <!-- SHIPPING -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-2">Shipping Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
            <p><strong>City:</strong> <?= htmlspecialchars($order['city']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
        </div>

        <!-- ITEMS -->
        <h3 class="text-xl font-semibold mb-3">Items Ordered</h3>

        <?php if (!empty($order_items)): ?>
            <table class="w-full border border-gray-300 mb-6">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 border">Product</th>
                        <th class="p-2 border">Quantity</th>
                        <th class="p-2 border">Price</th>
                        <th class="p-2 border">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr class="text-center">
                        <td class="p-2 border"><?= htmlspecialchars($item['product_name']); ?></td>
                        <td class="p-2 border"><?= $item['quantity']; ?></td>
                        <td class="p-2 border">₹<?= number_format($item['price'], 2); ?></td>
                        <td class="p-2 border">
                            ₹<?= number_format($item['price'] * $item['quantity'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600 mb-6">No items found for this order.</p>
        <?php endif; ?>

        <div class="text-right text-xl font-bold">
            Grand Total: ₹<?= number_format($order['total'], 2); ?>
        </div>

        <div class="mt-6 text-center">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Print Receipt
            </button>

            <a href="dashboard.php"
                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 ml-4">
                Back to Dashboard
            </a>
        </div>

    </div>
</div>

</body>
</html>