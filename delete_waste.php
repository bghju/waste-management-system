<?php
session_start();

// 1. Security: Allow both User and Admin to perform a delete
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "waste_db");
if (!$conn) die("Connection Failed");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 2. Capture the 'from' parameter from the URL
    $source = isset($_GET['from']) ? $_GET['from'] : 'user';

    // 3. Delete records (Child first, then Parent)
    // Deleting from waste_details first prevents Foreign Key errors
    mysqli_query($conn, "DELETE FROM waste_details WHERE waste_id = '$id'");
    $delete_main = mysqli_query($conn, "DELETE FROM waste WHERE id = '$id'");

    if ($delete_main) {
        // 4. THE FIX: Redirect back based on the source
        if ($source === 'admin') {
            header("Location: admin_dashboard.php?msg=deleted");
        } else {
            header("Location: view_waste.php?msg=deleted");
        }
        exit();
    }
}

// Fallback if something goes wrong
header("Location: index.php");
exit();
?>