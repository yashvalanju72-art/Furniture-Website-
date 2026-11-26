<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

if(!isset($_GET['order_id'])){
    die("No order specified.");
}

$order_id = intval($_GET['order_id']);

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

/* ==============================
   FETCH ORDER (FIXED: id NOT order_id)
   ============================== */
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
if(!$stmt){
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if(!$order){
    die("Order not found.");
}

/* ==============================
   FETCH ORDER ITEMS
   (Assuming order_items table uses order_id as foreign key)
   ============================== */
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
if(!$stmt){
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_items = [];
while($row = $result->fetch_assoc()){
    $order_items[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Order | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<div class="container mx-auto py-10">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">
        Order #<?php echo htmlspecialchars($order['id']); ?>
    </h2>

    <!-- Order Details Card -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
        <h3 class="text-2xl font-semibold mb-4 text-gray-700">Order Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-600">
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
            <p><strong>Total:</strong> ₹<?php echo number_format($order['total'],2); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>

            <?php if(isset($order['order_date'])): ?>
                <p><strong>Date:</strong>
                    <?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h3 class="text-2xl font-semibold mb-4 text-gray-700">Items Ordered</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">

                    <?php if(!empty($order_items)): ?>
                        <?php foreach($order_items as $item): ?>
                            <tr class="hover:bg-amber-50 transition">
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($item['product_id']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($item['quantity']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    ₹<?php echo number_format($item['price'],2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    ₹<?php echo number_format($item['quantity'] * $item['price'],2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center p-4 text-gray-500">
                                No items found for this order.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <a href="orders.php"
       class="mt-6 inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

</body>
</html>