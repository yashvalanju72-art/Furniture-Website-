<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(!isset($_GET['order_id'])){
    die("Order ID missing.");
}

$order_id = intval($_GET['order_id']);

// Fetch order
$result = $conn->query("SELECT * FROM orders WHERE order_id = $order_id");
if(!$result || $result->num_rows === 0){
    die("Order not found.");
}

$order = $result->fetch_assoc();
$error = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_id = intval($_POST['user_id']);
    $total = floatval($_POST['total']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);

    // Update order safely (Order ID is not changed)
    $update = $conn->query("UPDATE orders SET user_id=$user_id, total=$total, payment_method='$payment_method' WHERE order_id=$order_id");
    if($update){
        header("Location: orders.php");
        exit();
    } else {
        $error = "Update failed: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Order | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-amber-50 min-h-screen font-sans flex items-center justify-center">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6">Edit Order #<?php echo $order['order_id']; ?></h2>

    <?php if($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-medium mb-1">User ID</label>
            <input type="number" name="user_id" value="<?php echo $order['user_id']; ?>" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1">Total</label>
            <input type="number" step="0.01" name="total" value="<?php echo $order['total']; ?>" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1">Payment Method</label>
            <input type="text" name="payment_method" value="<?php echo htmlspecialchars($order['payment_method']); ?>" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="flex justify-between items-center">
            <a href="orders.php" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Update</button>
        </div>
    </form>
</div>

</body>
</html>
