<?php
session_start();

if(!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","waste_db");
if (!$conn) die("Database Connection Failed");

// Get selected month from URL, or default to current month (e.g., "2026-03")
$selected_month = isset($_GET['month_filter']) ? $_GET['month_filter'] : date('Y-m');

// Extract Year and Month for SQL queries
$year = date('Y', strtotime($selected_month));
$month = date('m', strtotime($selected_month));
$display_month = date('F Y', strtotime($selected_month)); // e.g., "March 2026"

/* 1. MONTHLY TOTALS */
$q_total = mysqli_query($conn, "SELECT SUM(d.quantity) as t FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE YEAR(w.date) = '$year' AND MONTH(w.date) = '$month'");
$total = mysqli_fetch_assoc($q_total)['t'] ?? 0;

$q_dry = mysqli_query($conn, "SELECT SUM(d.quantity) as d_val FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE d.category='Dry Waste' AND YEAR(w.date) = '$year' AND MONTH(w.date) = '$month'");
$dry = mysqli_fetch_assoc($q_dry)['d_val'] ?? 0;

$q_wet = mysqli_query($conn, "SELECT SUM(d.quantity) as w_val FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE d.category='Wet Waste' AND YEAR(w.date) = '$year' AND MONTH(w.date) = '$month'");
$wet = mysqli_fetch_assoc($q_wet)['w_val'] ?? 0;

$q_ewaste = mysqli_query($conn, "SELECT SUM(d.quantity) as e_val FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE d.category='E-Waste' AND YEAR(w.date) = '$year' AND MONTH(w.date) = '$month'");
$ewaste = mysqli_fetch_assoc($q_ewaste)['e_val'] ?? 0;

/* 2. DAILY TREND FOR CHART (Total waste grouped by day) */
$chart_data = [];
$chart_labels = [];
$q_chart = mysqli_query($conn, "
    SELECT DATE(w.date) as day_date, SUM(d.quantity) as daily_total 
    FROM waste w 
    JOIN waste_details d ON w.id = d.waste_id 
    WHERE YEAR(w.date) = '$year' AND MONTH(w.date) = '$month' 
    GROUP BY DATE(w.date) 
    ORDER BY day_date ASC
");
while($c_row = mysqli_fetch_assoc($q_chart)) {
    $chart_labels[] = date('d-M', strtotime($c_row['day_date']));
    $chart_data[] = $c_row['daily_total'];
}

/* 3. MONTHLY TABLE DATA */
$result = mysqli_query($conn, "
    SELECT w.id, w.date, w.block_name, w.campus_status,
    GROUP_CONCAT(CONCAT(d.category,': ',d.quantity,' kg') SEPARATOR '<br>') AS waste_details
    FROM waste w
    JOIN waste_details d ON w.id = d.waste_id
    WHERE YEAR(w.date) = '$year' AND MONTH(w.date) = '$month'
    GROUP BY w.id ORDER BY w.date DESC
");
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report | EcoCampus Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--bs-body-bg); overflow-x: hidden; transition: 0.3s; }

        /* ADMIN SIDEBAR - Matching exactly */
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: #111827; padding-top: 20px; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 800; text-align: center; margin-bottom: 30px; letter-spacing: 1px; }
        .sidebar-brand i { color: #10b981; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #9ca3af; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #1f2937; border-left: 4px solid #10b981; }

        .main-content { margin-left: 260px; padding: 30px; }

        /* Headers & Cards */
        .admin-header { background: var(--bs-body-bg); padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--bs-border-color); }
        .custom-card { background: var(--bs-body-bg); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; border: 1px solid var(--bs-border-color); margin-bottom: 25px; }
        
        /* Filter Box */
        .filter-box { background: #e0e7ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
        [data-bs-theme="dark"] .filter-box { background: #1e1e2f; border-color: #333; }

        /* Monthly Stats */
        .month-stat h6 { color: #6b7280; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
        .month-stat h3 { font-size: 24px; font-weight: 800; margin: 0; }
        .border-blue { border-left: 4px solid #3b82f6; padding-left: 15px; }
        .border-green { border-left: 4px solid #10b981; padding-left: 15px; }
        .border-orange { border-left: 4px solid #f59e0b; padding-left: 15px; }

        @media print {
            .sidebar, .admin-header, .filter-box { display: none !important; }
            .main-content { margin-left: 0; padding: 0; }
            body { background: white; }
            .custom-card { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-shield-halved"></i> Waste Admin</div>
    <a href="admin_dashboard.php"><i class="fa fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a>
    <a href="reports.php"><i class="fa fa-file"></i> Reports</a>
    <a href="monthly_report.php" class="active"><i class="fa fa-chart-pie"></i> Monthly Report</a>
</div>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Monthly Analytics</h4>
            <span class="text-muted" style="font-size: 14px;">Detailed report for <strong><?php echo $display_month; ?></strong></span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Print Report</button>
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="filter-box">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-filter text-primary fs-4 me-3"></i>
            <h6 class="mb-0 fw-bold me-3">Filter by Month:</h6>
            <form action="" method="GET" class="d-flex align-items-center">
                <input type="month" name="month_filter" class="form-control form-control-sm me-2" value="<?php echo $selected_month; ?>" required>
                <button type="submit" class="btn btn-sm btn-dark">Apply Filter</button>
            </form>
        </div>
        <?php if(isset($_GET['month_filter'])) { ?>
            <a href="monthly_report.php" class="btn btn-sm btn-outline-danger">Clear Filter</a>
        <?php } ?>
    </div>

    <div class="custom-card">
        <h5 class="mb-4 fw-bold"><i class="fa-solid fa-chart-pie me-2 text-secondary"></i> Totals for <?php echo $display_month; ?></h5>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="month-stat border-blue">
                    <h6>Total Waste</h6>
                    <h3><?php echo number_format($total, 1); ?> <span class="text-muted fs-6">kg</span></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="month-stat border-orange">
                    <h6>Dry Waste</h6>
                    <h3><?php echo number_format($dry, 1); ?> <span class="text-muted fs-6">kg</span></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="month-stat border-green">
                    <h6>Wet Waste</h6>
                    <h3><?php echo number_format($wet, 1); ?> <span class="text-muted fs-6">kg</span></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="month-stat" style="border-left: 4px solid #8b5cf6; padding-left: 15px;">
                    <h6>E-Waste</h6>
                    <h3><?php echo number_format($ewaste, 1); ?> <span class="text-muted fs-6">kg</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="custom-card">
                <h5 class="mb-4 fw-bold"><i class="fa-solid fa-arrow-trend-up me-2 text-secondary"></i> Daily Collection Trend (<?php echo $display_month; ?>)</h5>
                <canvas id="monthlyChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="custom-card">
        <h5 class="mb-4 fw-bold"><i class="fa-solid fa-list me-2 text-secondary"></i> Detailed Logs for <?php echo $display_month; ?></h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Block</th>
                        <th>Waste Breakdown</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0) {
                        while($row=mysqli_fetch_assoc($result)){ 
                    ?>
                    <tr>
                        <td class="fw-medium"><?php echo date('d-M-Y h:i A', strtotime($row['date'])); ?></td>
                        <td class="fw-bold"><?php echo $row['block_name']; ?></td>
                        <td><?php echo $row['waste_details']; ?></td>
                        <td>
                            <?php if($row['campus_status'] == "Inside"){ ?>
                                <span class="badge bg-danger">Inside</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Out</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No waste logged for this month.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    // Initialize Dark Mode based on local storage so it matches dashboard
    if(localStorage.getItem("theme") === "dark"){
        document.documentElement.setAttribute("data-bs-theme", "dark");
    }

    // Line Chart for Daily Trends
    var ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Total Daily Waste (kg)',
                data: <?php echo json_encode($chart_data); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#1d4ed8',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
</script>

</body>
</html>