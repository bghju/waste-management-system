<?php
session_start();

// Check if anyone is logged in
if(!isset($_SESSION['user']) && !isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","waste_db");

$id = $_GET['id'];
// Capture where the user came from (default to user if not specified)
$source = isset($_GET['from']) ? $_GET['from'] : 'user';

if(isset($_POST['update'])){
    $date = $_POST['date'];
    $block_name = $_POST['block_name'];
    $campus_status = $_POST['campus_status'];
    $categories = $_POST['category']; 
    $quantities = $_POST['quantity']; 

    // 1. Update main waste table
    mysqli_query($conn, "UPDATE waste SET date='$date', block_name='$block_name', campus_status='$campus_status' WHERE id='$id'");

    // 2. Sync Waste Details (Delete and Re-insert)
    mysqli_query($conn, "DELETE FROM waste_details WHERE waste_id='$id'");
    for ($i = 0; $i < count($categories); $i++) {
        $cat = mysqli_real_escape_string($conn, $categories[$i]);
        $qty = mysqli_real_escape_string($conn, $quantities[$i]);
        if(!empty($cat) && !empty($qty)) {
            mysqli_query($conn, "INSERT INTO waste_details (waste_id, category, quantity) VALUES ('$id', '$cat', '$qty')");
        }
    }

    // 3. THE FIX: Redirect based on the URL 'from' parameter
    if ($source === 'admin') {
        header("Location: admin_dashboard.php?msg=success");
    } else {
        header("Location: view_waste.php?msg=success");
    }
    exit();
}

// Fetch existing data
$res = mysqli_query($conn, "SELECT * FROM waste WHERE id='$id'");
$row = mysqli_fetch_assoc($res);
$details_res = mysqli_query($conn, "SELECT * FROM waste_details WHERE waste_id='$id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Waste Log</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container border bg-white p-4 shadow-sm" style="max-width: 700px; border-radius: 12px;">
    <h3>Edit Waste Record</h3>
    <hr>
    
    <form method="POST" action="edit_waste.php?id=<?php echo $id; ?>&from=<?php echo $source; ?>">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Date</label>
                <input type="datetime-local" name="date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($row['date'])); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Block</label>
                <select name="block_name" class="form-select">
                    <option value="IB Block" <?php if($row['block_name']=="IB Block") echo "selected"; ?>>IB Block</option>
                    <option value="AS Block" <?php if($row['block_name']=="AS Block") echo "selected"; ?>>AS Block</option>
                    <option value="SF Block" <?php if($row['block_name']=="SF Block") echo "selected"; ?>>SF Block</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <select name="campus_status" class="form-select">
                <option value="Inside" <?php if($row['campus_status']=="Inside") echo "selected"; ?>>Inside</option>
                <option value="Out" <?php if($row['campus_status']=="Out") echo "selected"; ?>>Out</option>
            </select>
        </div>

        <h5>Waste Items</h5>
        <?php while($d = mysqli_fetch_assoc($details_res)): ?>
        <div class="row mb-2">
            <div class="col-7">
                <input type="text" name="category[]" class="form-control" value="<?php echo $d['category']; ?>">
            </div>
            <div class="col-5">
                <input type="number" step="0.01" name="quantity[]" class="form-control" value="<?php echo $d['quantity']; ?>">
            </div>
        </div>
        <?php endwhile; ?>

        <div class="mt-4 pt-3 border-top">
            <button type="submit" name="update" class="btn btn-primary w-100 mb-2">Save Changes</button>
            
            <a href="<?php echo ($source === 'admin') ? 'admin_dashboard.php' : 'view_waste.php'; ?>" class="btn btn-outline-secondary w-100">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>