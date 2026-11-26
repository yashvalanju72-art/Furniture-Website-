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

/* ===============================
   START TRANSACTION (SAFE DELETE)
   =============================== */
$conn->begin_transaction();

try {

    // Delete order items first (foreign key dependency)
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    if(!$stmt){
        throw new Exception("Prepare failed (order_items): " . $conn->error);
    }

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // Delete order (FIXED: id instead of order_id)
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    if(!$stmt){
        throw new Exception("Prepare failed (orders): " . $conn->error);
    }

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

} catch (Exception $e) {

    // Rollback if error
    $conn->rollback();
    die("Delete failed: " . $e->getMessage());
}

$conn->close();

// Redirect back to orders page
header("Location: orders.php");
exit();
?>