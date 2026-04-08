<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "waste_db");

if (!$conn) {
    die("Database Connection Failed");
}

// Get the 5 most recent activities for the mini-table at the bottom
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
    <title>Home | EcoCampus Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* Modern Sidebar */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #1e293b;
            padding-top: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .sidebar-brand {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }
        .sidebar-brand i { color: #10b981; }
        
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 15px;
            color: #94a3b8;
            display: block;
            transition: 0.3s;
            font-weight: 500;
        }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #334155; border-left: 4px solid #10b981; }

        /* Main Content Area */
        .main-content { margin-left: 260px; padding: 40px; }
        
        /* Header Styling */
        .welcome-header { text-align: center; margin-bottom: 40px; }
        .welcome-header h2 { color: #0f172a; font-weight: 700; font-size: 28px; }
        .welcome-header p { color: #64748b; font-size: 16px; }

        /* Feature Cards (From your original design) */
        .feature-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px 20px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: 0.3s;
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .feature-icon { font-size: 40px; margin-bottom: 15px; }
        .feature-card h5 { color: #1e293b; font-weight: 700; margin-bottom: 15px; }
        .feature-card p { color: #3b82f6; font-weight: 600; font-size: 15px; margin: 0; line-height: 1.5; }

        /* Section Title */
        .section-title { text-align: center; color: #0f172a; font-weight: 700; margin: 40px 0 20px 0; font-size: 20px; }

        /* User Access Cards */
        .access-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-top: 4px solid #3b82f6;
        }
        .access-card.admin { border-top-color: #10b981; }
        .access-icon { font-size: 35px; color: #475569; margin-bottom: 15px; }
        .access-card h5 { font-weight: 700; color: #1e293b; }
        .access-card p { color: #3b82f6; font-weight: 600; margin-bottom: 0; }

        /* Recent Table Card */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-top: 40px;
        }
        .badge-inside { background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-out { background-color: #f0fdf4; color: #10b981; border: 1px solid #bbf7d0; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-leaf"></i> EcoCampus</div>
    
    <a href="index.php" class="active"><i class="fa-solid fa-house"></i> Home</a>
    <a href="user_student_view.php"><i class="fa-solid fa-users"></i> Student View</a>
    <a href="add_waste.php"><i class="fa-solid fa-plus-circle"></i> Log Waste</a>
    <a href="view_waste.php"><i class="fa-solid fa-chart-line"></i> Waste Report</a>
    
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    
    <div class="welcome-header">
        <h2>Welcome to Academic Waste Management System</h2>
        <p>A smart solution to track campus waste, manage disposal, and create environmental awareness.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="feature-card">
                <div class="feature-icon" style="color: #8b5cf6;"><i class="fa-solid fa-chart-column"></i></div>
                <h5>Waste Tracking</h5>
                <p>Monitor daily waste generation across the campus.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-card">
                <div class="feature-icon" style="color: #10b981;"><i class="fa-solid fa-recycle"></i></div>
                <h5>Waste Segregation</h5>
                <p>Classify waste into dry, wet, and e-waste categories.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-card">
                <div class="feature-icon" style="color: #f59e0b;"><i class="fa-solid fa-truck-fast"></i></div>
                <h5>Campus Disposal</h5>
                <p>Track whether waste is inside campus or moved out.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="feature-card">
                <div class="feature-icon" style="color: #3b82f6;"><i class="fa-solid fa-earth-americas"></i></div>
                <h5>Awareness</h5>
                <p>Educate students about eco-friendly waste management.</p>
            </div>
        </div>
    </div>

    <h4 class="section-title">User Access</h4>
    <div class="row g-4 justify-content-center">
        <div class="col-md-5">
            <div class="access-card">
                <div class="access-icon"><i class="fa-solid fa-user"></i></div>
                <h5>User</h5>
                <p>Login and record campus waste collection.</p>
            </div>
        </div>
        <div class="col-md-5">
            <div class="access-card admin">
                <div class="access-icon"><i class="fa-solid fa-user-tie"></i></div>
                <h5>Admin</h5>
                <p>Monitor waste records, edit data, and view reports.</p>
            </div>
        </div>
    </div>

    <div class="table-card">
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
                        <td class="text-muted fw-medium">
                            <i class="fa-regular fa-clock me-1"></i> <?php echo date('d-M-Y h:i A', strtotime($row['date'])); ?>
                        </td>
                        <td class="fw-bold text-dark"><?php echo $row['block_name']; ?></td>
                        <td><?php echo $row['details']; ?></td>
                        <td>
                            <?php if ($row['campus_status'] == 'Inside') { ?>
                                <span class="badge-inside"><i class="fa-solid fa-box-archive me-1"></i> Inside</span>
                            <?php } else { ?>
                                <span class="badge-out"><i class="fa-solid fa-truck-fast me-1"></i> Out</span>
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