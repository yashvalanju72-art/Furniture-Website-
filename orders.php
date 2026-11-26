<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");

if(!$orders){
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<div class="container mx-auto py-10">

    <!-- Top Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Orders</h2>

        <!-- Back to Dashboard Button -->
        <a href="admin_dashboard.php"
           class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto rounded-xl shadow-lg bg-white border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

            <?php if($orders->num_rows > 0): ?>
                <?php while($row = $orders->fetch_assoc()): ?>
                    <tr class="hover:bg-amber-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-700">
                            <?php echo htmlspecialchars($row['id']); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            <?php echo htmlspecialchars($row['user_id']); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            ₹<?php echo number_format($row['total'], 2); ?>
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            <?php echo htmlspecialchars($row['payment_method']); ?>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">

                                <a href="view_order.php?order_id=<?php echo $row['id']; ?>" 
                                   class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>

                                <a href="delete_order.php?order_id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this order?');"
                                   class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center p-4 text-gray-500">
                        No orders found.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>