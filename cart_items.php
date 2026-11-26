<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ 
    header("Location: admin_login.php"); 
    exit(); 
}

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$carts = $conn->query("SELECT ci.*, u.name as user_name, p.name as product_name FROM cart_items ci 
                       JOIN users u ON ci.user_id=u.id 
                       JOIN products p ON ci.product_id=p.id ORDER BY ci.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart Items | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-b from-amber-50 via-amber-100 to-amber-50 min-h-screen font-sans">

<div class="container mx-auto py-12">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-xl p-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">Cart Items</h2>
        <div class="overflow-x-auto rounded-lg shadow-inner">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php while($row=$carts->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-gray-700"><?php echo $row['id'];?></td>
                        <td class="px-6 py-3 text-gray-700"><?php echo htmlspecialchars($row['user_name']);?></td>
                        <td class="px-6 py-3 text-gray-700"><?php echo htmlspecialchars($row['product_name']);?></td>
                        <td class="px-6 py-3 text-gray-700"><?php echo $row['quantity'];?></td>
                        <td class="px-6 py-3">
                            <a href="delete_cart_item.php?id=<?php echo $row['id'];?>" onclick="return confirm('Delete this cart item?');" 
                               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 justify-center">
                               <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile;?>
                <?php if($carts->num_rows == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center p-6 text-gray-500">No cart items found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="admin_dashboard.php" class="mt-6 inline-block text-blue-600 hover:underline text-center w-full">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
