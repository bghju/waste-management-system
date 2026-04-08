<?php
session_start();

/* CHECK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/* DATABASE CONNECTION */
$conn = mysqli_connect("localhost", "root", "", "waste_db");

if (!$conn) {
    die("Database connection failed");
}

// Bring in the email script we just fixed!
require __DIR__ . '/send_mail.php';

/* UPDATE STATUS & SEND EMAIL */
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Get the waste details BEFORE we update it
    $query = "SELECT w.block_name, 
                     GROUP_CONCAT(d.category SEPARATOR ', ') as all_categories, 
                     SUM(d.quantity) as total_qty 
              FROM waste w 
              JOIN waste_details d ON w.id = d.waste_id 
              WHERE w.id = '$id' 
              GROUP BY w.id";
              
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $block = $data['block_name'];
        $categories = $data['all_categories'];
        $total_qty = $data['total_qty'];

        // 2. Update status to 'Out' in the database
        mysqli_query($conn, "UPDATE waste SET campus_status='Out' WHERE id='$id'");

        // 3. Send the real email!
        sendWasteAlert($block, $categories, $total_qty);
    }
}

/* RETURN TO REPORT PAGE */
header("Location: view_waste.php");
exit();
?>