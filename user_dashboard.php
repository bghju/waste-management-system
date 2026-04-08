<?php
// User Dashboard - No admin privileges required
$conn = mysqli_connect("localhost", "root", "", "waste_db");

if (!$conn) {
    die("Database Connection Failed");
}

// Get total waste for a quick student stat
$q_total = mysqli_query($conn, "SELECT SUM(quantity) as total FROM waste_details");
$tot_waste = mysqli_fetch_assoc($q_total)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | EcoCampus</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* User Panel Sidebar - Dark Blue to distinguish from Admin's Slate */
        .sidebar {
            height: 100vh; width: 260px; position: fixed; top: 0; left: 0;
            background-color: #0f172a; 
            padding-top: 20px; box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 30px; letter-spacing: 1px; }
        .sidebar-brand i { color: #3b82f6; } /* Blue Icon */
        
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #94a3b8; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #1e293b; border-left: 4px solid #3b82f6; }

        .main-content { margin-left: 260px; padding: 40px; }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 15px;
            padding: 40px;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner h2 { font-weight: 700; margin-bottom: 10px; }
        .welcome-banner p { font-size: 16px; opacity: 0.9; margin-bottom: 0; max-width: 600px; }
        .banner-icon { position: absolute; right: 40px; top: 50%; transform: translateY(-50%); font-size: 100px; opacity: 0.2; }

        /* Action Cards */
        .action-card {
            background: #ffffff; border-radius: 12px; padding: 30px; text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: 0.3s; border: 1px solid #e2e8f0;
            display: block; text-decoration: none; color: inherit; height: 100%;
        }
        .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: #cbd5e1; color: inherit; }
        
        .action-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 20px auto; }
        .icon-report { background-color: #eff6ff; color: #3b82f6; }
        .icon-awareness { background-color: #f0fdf4; color: #10b981; }
        
        .action-card h4 { font-weight: 700; color: #1e293b; margin-bottom: 10px; }
        .action-card p { color: #64748b; font-size: 14px; margin-bottom: 0; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i> Student Portal</div>
    
    <a href="user_dashboard.php" class="active"><i class="fa-solid fa-house-user"></i> Dashboard</a>
    <a href="student_view.php"><i class="fa-solid fa-chart-pie"></i> Campus Report</a>
    <a href="awareness.php"><i class="fa-solid fa-lightbulb"></i> Awareness</a>
    
    <div style="margin-top: 50px; border-top: 1px solid #1e293b; padding-top: 20px;">
        <a href="login.php" style="color: #64748b;"><i class="fa-solid fa-shield-halved"></i> Admin Login</a>
    </div>
</div>

<div class="main-content">
    
    <div class="welcome-banner">
        <i class="fa-solid fa-leaf banner-icon"></i>
        <h2>Welcome to EcoCampus</h2>
        <p>Join our initiative to maintain a clean, green, and sustainable campus environment. Track our progress and learn how you can contribute to reducing waste.</p>
        <div class="mt-4">
            <span class="badge bg-light text-primary fs-6 px-3 py-2 rounded-pill">
                <i class="fa-solid fa-scale-balanced me-1"></i> <?php echo number_format($tot_waste, 1); ?> kg Total Campus Waste
            </span>
        </div>
    </div>

    <div class="row g-4 mt-2">
        
        <div class="col-md-6">
            <a href="student_view.php" class="action-card">
                <div class="action-icon icon-report">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <h4>View Campus Report</h4>
                <p>See real-time data on how much dry, wet, and e-waste our campus is generating and where it is going.</p>
            </a>
        </div>

        <div class="col-md-6">
            <a href="awareness.php" class="action-card">
                <div class="action-icon icon-awareness">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
                <h4>Learn & Act</h4>
                <p>Read our awareness guidelines on proper waste segregation and eco-friendly practices on campus.</p>
            </a>
        </div>

    </div>

</div>

</body>
</html>