<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

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

/* ================= FETCH CART PRODUCTS ================= */

$cart_products = [];
$total = 0;

if (!empty($_SESSION['cart'])) {

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");

    if ($stmt) {
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = $_SESSION['cart'][$row['id']];
            $cart_products[] = $row;
            $total += $row['price'] * $row['quantity'];
        }

        $stmt->close();
    } else {
        die("Product fetch failed: " . $conn->error);
    }
}

/* ================= HANDLE CHECKOUT ================= */

$error = "";

if (isset($_POST['checkout']) && !empty($cart_products)) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $phone = trim($_POST['phone']);
    $payment_method = $_POST['payment_method'];

    /* ===== PAYMENT VALIDATION ===== */

    if ($payment_method == "Card") {

        $card_number = trim($_POST['card_number']);
        $card_name   = trim($_POST['card_name']);
        $expiry      = trim($_POST['expiry']);
        $cvv         = trim($_POST['cvv']);

        if (!preg_match('/^[0-9]{16}$/', $card_number)) {
            $error = "Card number must be exactly 16 digits.";
        } 
        elseif (!preg_match('/^[a-zA-Z ]{3,50}$/', $card_name)) {
            $error = "Enter valid cardholder name.";
        } 
        elseif (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry)) {
            $error = "Expiry must be MM/YY.";
        } 
        else {
            list($month, $year) = explode('/', $expiry);
            $currentYear = date('y');
            $currentMonth = date('m');

            if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
                $error = "Card expired.";
            }
        }

        if (empty($error) && !preg_match('/^[0-9]{3}$/', $cvv)) {
            $error = "CVV must be 3 digits.";
        }
    }

    if ($payment_method == "UPI") {

        $upi_id = trim($_POST['upi_id']);

        if (strlen($upi_id) > 50) {
            $error = "UPI ID too long.";
        }
        elseif (!preg_match('/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/', $upi_id)) {
            $error = "Invalid UPI ID.";
        }
    }

    /* ===== IF NO ERROR, PROCESS ORDER ===== */

    if (empty($error)) {

        $stmt = $conn->prepare("INSERT INTO orders 
            (user_id, total, name, email, address, city, phone, payment_method) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Order prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "idssssss",
            $user_id,
            $total,
            $name,
            $email,
            $address,
            $city,
            $phone,
            $payment_method
        );

        if (!$stmt->execute()) {
            die("Order execute failed: " . $stmt->error);
        }

        $order_id = $stmt->insert_id;
        $stmt->close();

        /* ===== INSERT ORDER ITEMS ===== */

        $stmt = $conn->prepare("INSERT INTO order_items 
            (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)");

        foreach ($cart_products as $product) {

            $product_id = $product['id'];
            $quantity = $product['quantity'];
            $price = $product['price'];

            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }

        $stmt->close();

        $_SESSION['cart'] = [];

        $clearStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        if ($clearStmt) {
            $clearStmt->bind_param("i", $user_id);
            $clearStmt->execute();
            $clearStmt->close();
        }

        $success = "Order placed successfully! Your order ID is #$order_id.";

        /* ===== SEND EMAIL ===== */

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yourgmail@gmail.com'; // YOUR EMAIL
            $mail->Password   = 'your_app_password';   // APP PASSWORD
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'FurniFi Store');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation - FurniFi';

            $mail->Body = "
                <h2>Order Successful 🎉</h2>
                <p>Hi $name,</p>
                <p>Your order has been placed successfully.</p>
                <p><strong>Order ID:</strong> #$order_id</p>
                <p><strong>Total:</strong> ₹$total</p>
                <p>Thank you for shopping with FurniFi!</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            // silently fail
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
<title>Checkout | FurniFi</title>
<script src="https://cdn.tailwindcss.com"></script>

<script>
function togglePaymentDetails() {
    const cardSection = document.getElementById("card-details");
    const upiSection = document.getElementById("upi-details");

    const cardInputs = cardSection.querySelectorAll("input");
    const upiInputs = upiSection.querySelectorAll("input");

    const method = document.querySelector('input[name="payment_method"]:checked').value;

    // Remove required first
    cardInputs.forEach(input => input.required = false);
    upiInputs.forEach(input => input.required = false);

    if (method === "Card") {
        cardSection.classList.remove("hidden");
        upiSection.classList.add("hidden");

        cardInputs.forEach(input => input.required = true);

    } else if (method === "UPI") {
        upiSection.classList.remove("hidden");
        cardSection.classList.add("hidden");

        upiInputs.forEach(input => input.required = true);

    } else {
        cardSection.classList.add("hidden");
        upiSection.classList.add("hidden");
    }
}

window.onload = togglePaymentDetails;
</script>

</head>

<body class="bg-gray-50 min-h-screen">

<!-- Navbar -->
<nav class="bg-[#f5f5dc] shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">FurniFi Shop</h1>
    <div class="flex items-center space-x-4">
        <b><a href="index.html" class="text-red-600 hover:underline">Home</a></b>
        <b><a href="shop.php" class="text-green-600 hover:underline">Shop</a></b>
        <b><a href="cart.php" class="text-blue-600 hover:underline">Cart</a></b>
        <b><a href="logout.php" class="text-red-600 hover:underline">Logout</a></b>
    </div>
</nav>

<div class="container mx-auto py-10 px-4">
    <h2 class="text-3xl font-bold mb-6">Checkout</h2>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-700 p-4 mb-6 rounded"><?php echo $success; ?></div>

        <div class="flex justify-between mt-4">
            <a href="receipt.php?order_id=<?php echo $order_id; ?>" target="_blank" 
               class="bg-yellow-500 text-white px-6 py-3 rounded hover:bg-yellow-600">
                Print Receipt
            </a>

            <a href="shop.php" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>

    <?php elseif (empty($cart_products)): ?>

        <p class="text-gray-600">
            Your cart is empty. 
            <a href="shop.php" class="text-green-600 hover:underline">Go shopping</a>.
        </p>

    <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Cart Summary -->
            <div>
                <h3 class="text-2xl font-semibold mb-4">Order Summary</h3>

                <div class="space-y-4">
                    <?php foreach($cart_products as $product): ?>
                        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                            <span>
                                <?php echo htmlspecialchars($product['name']); ?> 
                                x <?php echo $product['quantity']; ?>
                            </span>
                            <span>
                                ₹<?php echo number_format($product['price'] * $product['quantity'], 2); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 text-right font-bold text-xl">
                    Total: ₹<?php echo number_format($total, 2); ?>
                </div>
            </div>

            <!-- Form -->
            <div>
                <h3 class="text-2xl font-semibold mb-4">Shipping & Payment</h3>

                <form method="POST" class="space-y-4">

                    <input type="text" name="name" placeholder="Full Name" required class="w-full border rounded px-3 py-2">

                    <input type="email" name="email" placeholder="Email" required class="w-full border rounded px-3 py-2">

                    <textarea name="address" placeholder="Address" required class="w-full border rounded px-3 py-2"></textarea>

                    <input type="text" name="city" placeholder="City" required class="w-full border rounded px-3 py-2">

                    <input type="text" name="phone" pattern="[0-9]{10}" maxlength="10" 
                        placeholder="Phone Number" class="w-full border rounded px-3 py-2">

                    <!-- Payment -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">Payment Method</h4>

                        <label class="block">
                            <input type="radio" name="payment_method" value="COD" checked onclick="togglePaymentDetails()"> 
                            Cash on Delivery
                        </label>

                        <label class="block">
                            <input type="radio" name="payment_method" value="Card" onclick="togglePaymentDetails()"> 
                            Credit / Debit Card
                        </label>

                        <label class="block">
                            <input type="radio" name="payment_method" value="UPI" onclick="togglePaymentDetails()"> 
                            UPI
                        </label>
                    </div>

                    <!-- Card -->
                    <div id="card-details" class="hidden space-y-3 mt-4">
                        <input type="text" name="card_number" placeholder="Card Number" class="w-full border rounded px-3 py-2">

                        <input type="text" name="card_name" placeholder="Cardholder Name" class="w-full border rounded px-3 py-2">

                        <div class="flex space-x-2">
                            <input type="text" name="expiry" placeholder="MM/YY" class="w-1/2 border rounded px-3 py-2">

                            <input type="text" name="cvv" placeholder="CVV" class="w-1/2 border rounded px-3 py-2">
                        </div>
                    </div>

                    <!-- UPI -->
                    <div id="upi-details" class="hidden space-y-3 mt-4">
                        <input type="text" name="upi_id" placeholder="Enter UPI ID (e.g., name@upi)" 
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <button type="submit" name="checkout" 
                        class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 w-full">
                        Place Order
                    </button>

                </form>
            </div>

        </div>

    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-[#a67c52] text-gray-200 py-12">
    <div class="container mx-auto px-6 text-center space-y-6">
        <div class="flex justify-center gap-8 text-2xl">
            <a href="#" class="hover:text-green-500"><i class="fab fa-facebook"></i></a>
            <a href="#" class="hover:text-green-500"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-green-500"><i class="fab fa-instagram"></i></a>
        </div>

        <p class="text-gray-400">&copy; 2026 FurniFi Furniture. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
