<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "waste_db");
if (!$conn) {
    die("Database connection failed");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "UPDATE waste SET campus_status='Out' WHERE id=$id";
    mysqli_query($conn, $query);
}

header("Location: view_waste.php");
exit();
?>
