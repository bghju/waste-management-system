<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "waste_db");
if (!$conn) die("Database Connection Failed");

$q_total = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details");
$tot_waste = mysqli_fetch_assoc($q_total)['total'] ?? 0;

$q_dry = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details WHERE category='Dry Waste'");
$dry_waste = mysqli_fetch_assoc($q_dry)['total'] ?? 0;

$q_wet = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details WHERE category='Wet Waste'");
$wet_waste = mysqli_fetch_assoc($q_wet)['total'] ?? 0;

$result = mysqli_query($conn, "
    SELECT w.date, w.block_name, w.campus_status, 
    GROUP_CONCAT(CONCAT(d.category, ': ', d.quantity, ' kg') SEPARATOR ', ') AS waste_details 
    FROM waste w JOIN waste_details d ON w.id = d.waste_id GROUP BY w.id ORDER BY w.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student View | User Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        
        /* THIS IS YOUR USER SIDEBAR */
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: #1e293b; padding-top: 20px; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 1000; }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 30px; }
        .sidebar-brand i { color: #10b981; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #94a3b8; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #334155; border-left: 4px solid #10b981; }

        .main-content { margin-left: 260px; padding: 30px; }
        .custom-card { background: #ffffff; border-radius: 12px; padding: 25px; margin-top: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-leaf"></i> EcoCampus</div>
    
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    
    <a href="user_student_view.php" class="active"><i class="fa-solid fa-users"></i> Student View</a>
    
    <a href="add_waste.php"><i class="fa-solid fa-plus-circle"></i> Log Waste</a>
    <a href="view_waste.php"><i class="fa-solid fa-chart-line"></i> Manage Waste</a>
    
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <h3 class="mb-4"><i class="fa-solid fa-users text-primary me-2"></i> Student View Data</h3>
    
    <div class="row g-4 mb-4">
        <div class="col-md-4"><div class="custom-card"><h6>Total Waste</h6><h3><?php echo number_format($tot_waste, 1); ?> kg</h3></div></div>
        <div class="col-md-4"><div class="custom-card"><h6>Dry Waste</h6><h3><?php echo number_format($dry_waste, 1); ?> kg</h3></div></div>
        <div class="col-md-4"><div class="custom-card"><h6>Wet Waste</h6><h3><?php echo number_format($wet_waste, 1); ?> kg</h3></div></div>
    </div>

    <div class="custom-card table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Date</th><th>Block</th><th>Details</th><th>Status</th></tr></thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo date('d-M-Y h:i A', strtotime($row['date'])); ?></td>
                    <td class="fw-bold"><?php echo $row['block_name']; ?></td>
                    <td><?php echo $row['waste_details']; ?></td>
                    <td><?php echo $row['campus_status']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>