<?php
// Student Dashboard - No login required, 100% Read-Only
$conn = mysqli_connect("localhost", "root", "", "waste_db");

if (!$conn) {
    die("Database Connection Failed");
}

// 1. Calculate Summary Stats for Students
$q_total = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details");
$tot_waste = mysqli_fetch_assoc($q_total)['total'] ?? 0;

$q_dry = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details WHERE category='Dry Waste'");
$dry_waste = mysqli_fetch_assoc($q_dry)['total'] ?? 0;

$q_wet = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details WHERE category='Wet Waste'");
$wet_waste = mysqli_fetch_assoc($q_wet)['total'] ?? 0;

// 2. Get the 5 most recent activities for the mini-table
$recent_logs = mysqli_query($conn, "
    SELECT w.date, w.block_name, w.campus_status, 
    GROUP_CONCAT(CONCAT(d.category, ': ', d.quantity, ' kg') SEPARATOR ', ') AS details 
    FROM waste w 
    JOIN waste_details d ON w.id = d.waste_id 
    GROUP BY w.id 
    ORDER BY w.id DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | EcoCampus</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* Student Sidebar - Dark Blue Theme */
        .sidebar {
            height: 100vh; width: 260px; position: fixed; top: 0; left: 0;
            background-color: #0f172a; padding-top: 20px; box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 30px; letter-spacing: 1px; }
        .sidebar-brand i { color: #3b82f6; }
        
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #94a3b8; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #1e293b; border-left: 4px solid #3b82f6; }

        .main-content { margin-left: 260px; padding: 30px; }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 12px; padding: 30px; color: white; margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2); position: relative; overflow: hidden;
        }
        .welcome-banner h3 { font-weight: 700; margin-bottom: 5px; }
        .welcome-banner p { margin-bottom: 0; opacity: 0.9; }
        .banner-icon { position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 80px; opacity: 0.2; }

        /* Stat Cards */
        .stat-card {
            background: #ffffff; border-radius: 12px; padding: 20px; border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; justify-content: space-between;
        }
        .stat-info h6 { color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 5px; text-transform: uppercase; }
        .stat-info h3 { color: #0f172a; font-size: 24px; font-weight: 700; margin: 0; }
        .stat-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        .icon-blue { background-color: #eff6ff; color: #3b82f6; }
        .icon-orange { background-color: #fff7ed; color: #f97316; }
        .icon-green { background-color: #f0fdf4; color: #10b981; }

        /* Table Card */
        .custom-card { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 25px; margin-top: 30px; }
        .badge-inside { background-color: #fef2f2; color: #ef4444; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-out { background-color: #f0fdf4; color: #10b981; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i> Student Portal</div>
    
    <a href="student_dashboard.php" class="active"><i class="fa-solid fa-house-user"></i> Dashboard</a>
    <a href="student_view.php"><i class="fa-solid fa-chart-pie"></i> Full Report</a>
    <a href="awareness.php"><i class="fa-solid fa-lightbulb"></i> Awareness</a>
</div>

<div class="main-content">
    
    <div class="welcome-banner">
        <i class="fa-solid fa-leaf banner-icon"></i>
        <h3>Welcome, Student!</h3>
        <p>Monitor our campus environmental impact and learn how to segregate waste properly.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>Total Campus Waste</h6>
                    <h3><?php echo number_format($tot_waste, 1); ?> <span style="font-size:14px; color:#94a3b8;">kg</span></h3>
                </div>
                <div class="stat-icon icon-blue"><i class="fa-solid fa-scale-unbalanced"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>Total Dry Waste</h6>
                    <h3><?php echo number_format($dry_waste, 1); ?> <span style="font-size:14px; color:#94a3b8;">kg</span></h3>
                </div>
                <div class="stat-icon icon-orange"><i class="fa-solid fa-box-open"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>Total Wet Waste</h6>
                    <h3><?php echo number_format($wet_waste, 1); ?> <span style="font-size:14px; color:#94a3b8;">kg</span></h3>
                </div>
                <div class="stat-icon icon-green"><i class="fa-solid fa-apple-whole"></i></div>
            </div>
        </div>
    </div>

    <div class="custom-card">
        <h5 class="mb-4 fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Recent Campus Activity</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Block</th>
                        <th>Waste Details</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($recent_logs) > 0) {
                        while ($row = mysqli_fetch_assoc($recent_logs)) { 
                    ?>
                    <tr>
                        <td class="text-muted fw-medium"><?php echo date('d-M-Y h:i A', strtotime($row['date'])); ?></td>
                        <td class="fw-bold text-dark"><?php echo $row['block_name']; ?></td>
                        <td><?php echo $row['details']; ?></td>
                        <td>
                            <?php if ($row['campus_status'] == 'Inside') { ?>
                                <span class="badge-inside">Inside</span>
                            <?php } else { ?>
                                <span class="badge-out">Out</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted'>No recent activity found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>