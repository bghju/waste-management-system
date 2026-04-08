<?php
session_start();

if(!isset($_SESSION['user'])){
header("Location: login.php");
exit();
}

$conn = mysqli_connect("localhost","root","","waste_db");

if(!$conn){
die("Database connection failed");
}

include "send_mail.php";

$id = $_GET['id'];

/* get waste details */

$query = "
SELECT w.block_name, wd.category, wd.quantity
FROM waste w
JOIN waste_details wd ON w.id = wd.waste_id
WHERE w.id = '$id'
";

$result = mysqli_query($conn,$query);

$data = mysqli_fetch_assoc($result);

$block = $data['block_name'];
$category = $data['category'];
$qty = $data['quantity'];

/* update status */

mysqli_query($conn,"UPDATE waste SET campus_status='Out' WHERE id='$id'");

/* send email */

sendWasteAlert($block,$category,$qty);

/* redirect */

header("Location:view_waste.php");

exit();
?>