<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){ header("Location: admin_login.php"); exit(); }
$conn = new mysqli("localhost","root","","furniture_db");
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$id=intval($_GET['id']);
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: products.php");
exit();
