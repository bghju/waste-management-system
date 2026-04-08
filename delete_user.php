<?php
$conn = mysqli_connect("localhost","root","","waste_db");

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM users WHERE id=$id");

header("Location: manage_users.php");
?>