<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin_login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Invalid request.");
}

$id = intval($_GET['id']);

$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    header("Location: contact_messages.php");
    exit();
}else{
    die("Delete failed: ".$conn->error);
}
?>