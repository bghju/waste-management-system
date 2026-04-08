<?php
session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","waste_db");
if (!$conn) die("Database Connection Failed");

/* SEARCH VALUES */
$search="";
$date="";

if(isset($_GET['search'])){
    $search=$_GET['search'];
}
if(isset($_GET['date'])){
    $date=$_GET['date'];
}

/* FETCH TABLE DATA WITH SEARCH */
$query="
    SELECT w.id, w.date, w.block_name, w.campus_status,
    GROUP_CONCAT(CONCAT(d.category,': ',d.quantity,' kg')) AS waste_details
    FROM waste w
    JOIN waste_details d ON w.id = d.waste_id
    WHERE 1
";

if($search!=""){
    $query.=" AND w.block_name LIKE '%$search%'";
}
if($date!=""){
    $query.=" AND DATE(w.date)='$date'";
}
$query.=" GROUP BY w.id ORDER BY w.id DESC";
$result=mysqli_query($conn,$query);

/* DASHBOARD TOTALS (Added ?? 0 to prevent errors if empty) */
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as t FROM waste_details"))['t'] ?? 0;
$dry = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as d FROM waste_details WHERE category='Dry'"))['d'] ?? 0;
$wet = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as w FROM waste_details WHERE category='Wet'"))['w'] ?? 0;
$ewaste = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(quantity) as e FROM waste_details WHERE category='E-Waste'"))['e'] ?? 0;

/* TOTAL WASTE BY BLOCK */
$block_query="
    SELECT w.block_name, SUM(d.quantity) as total_block_waste
    FROM waste w
    JOIN waste_details d ON w.id = d.waste_id
    GROUP BY w.block_name
    ORDER BY total_block_waste DESC
";
$block_result=mysqli_query($conn,$block_query);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | EcoCampus</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--bs-body-bg); overflow-x: hidden; transition: 0.3s; }

        /* ADMIN SIDEBAR - Deep Midnight Theme */
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: #111827; padding-top: 20px; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 800; text-align: center; margin-bottom: 30px; letter-spacing: 1px; }
        .sidebar-brand i { color: #10b981; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #9ca3af; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #1f2937; border-left: 4px solid #10b981; }

        .main-content { margin-left: 260px; padding: 30px; }

        /* Admin Header Banner */
        .admin-header { background: var(--bs-body-bg); padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--bs-border-color); }
        .admin-header h4 { margin: 0; font-weight: 700; }

        /* Admin Stat Cards */
        .stat-card { background: var(--bs-body-bg); border-radius: 12px; padding: 25px; border: 1px solid var(--bs-border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; overflow: hidden; }
        .stat-card::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 4px; }
        .border-blue::before { background-color: #3b82f6; }
        .border-green::before { background-color: #10b981; }
        .border-orange::before { background-color: #f59e0b; }
        .border-purple::before { background-color: #8b5cf6; }

        .stat-info h6 { color: #6b7280; font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        .stat-info h2 { font-size: 28px; font-weight: 800; margin: 0; }
        
        /* Custom Cards for Tables & Charts */
        .custom-card { background: var(--bs-body-bg); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; border: 1px solid var(--bs-border-color); margin-bottom: 25px; }
        
        /* Badges */
        .badge-inside { background-color: rgba(220, 38, 38, 0.1); color: #dc2626; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #fca5a5; }
        .badge-out { background-color: rgba(5, 150, 105, 0.1); color: #059669; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #6ee7b7; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-shield-halved"></i> Waste Admin</div>
    
    <a href="admin_dashboard.php" class="active"><i class="fa fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fa fa-users"></i> Manage Users</a>
    <a href="reports.php"><i class="fa fa-file"></i> Reports</a>
    <a href="monthly_report.php"><i class="fa fa-chart-pie"></i> Monthly Report</a>
</div>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4><i class="fa-solid fa-toolbox text-success me-2"></i> System Control Panel</h4>
            <span class="text-muted" style="font-size: 14px;">Monitor and manage all campus waste operations</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="toggleDarkMode()" class="btn btn-dark"><i class="fa-solid fa-moon"></i> Dark Mode</button>
            <a href="admin_logout.php" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-blue">
                <div class="stat-info">
                    <h6>Total Waste</h6>
                    <h2><?php echo number_format($total, 1); ?> <span style="font-size:16px; color:#9ca3af;">kg</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-orange">
                <div class="stat-info">
                    <h6>Dry Waste</h6>
                    <h2><?php echo number_format($dry, 1); ?> <span style="font-size:16px; color:#9ca3af;">kg</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-green">
                <div class="stat-info">
                    <h6>Wet Waste</h6>
                    <h2><?php echo number_format($wet, 1); ?> <span style="font-size:16px; color:#9ca3af;">kg</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-purple">
                <div class="stat-info">
                    <h6>E-Waste</h6>
                    <h2><?php echo number_format($ewaste, 1); ?> <span style="font-size:16px; color:#9ca3af;">kg</span></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-card">
        <h5 class="mb-4 fw-bold"><i class="fa-solid fa-database me-2 text-secondary"></i> Master Waste Records</h5>
        <div class="table-responsive">
            <table id="adminTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Block</th>
                        <th>Waste Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row=mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td class="fw-medium"><?php echo $row['date']; ?></td>
                        <td class="fw-bold"><?php echo $row['block_name']; ?></td>
                        <td><?php echo $row['waste_details']; ?></td>
                        <td>
                            <?php if($row['campus_status']=="Inside"){ ?>
                                <span class="badge-inside">Inside</span>
                            <?php }else{ ?>
                                <span class="badge-out">Out</span>
                            <?php } ?>
                        </td>
                       <td>
    <a href="edit_waste.php?id=<?php echo $row['id']; ?>&from=admin" class="btn btn-sm btn-primary">
        <i class="fa-solid fa-pen"></i> Edit
    </a>
    
    <a href="delete_waste.php?id=<?php echo $row['id'];?>&from=admin" 
       class="btn btn-sm btn-danger" 
       onclick="return confirm('Are you sure you want to delete this record?')">
       <i class="fa-solid fa-trash"></i>
    </a>
</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="custom-card h-100">
                <h5 class="mb-4 fw-bold"><i class="fa-solid fa-chart-bar me-2 text-secondary"></i> Waste Distribution</h5>
                <canvas id="wasteChart"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="custom-card h-100">
                <h5 class="mb-4 fw-bold"><i class="fa-solid fa-building me-2 text-secondary"></i> Total Waste by Blocks</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Block Name</th>
                                <th>Total Waste Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($block=mysqli_fetch_assoc($block_result)){ ?>
                            <tr>
                                <td class="fw-bold"><?php echo $block['block_name']; ?></td>
                                <td class="text-primary fw-medium"><?php echo number_format($block['total_block_waste'], 1); ?> kg</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#adminTable').DataTable({
            "pageLength": 10,
            "order": [], 
            "language": { "search": "_INPUT_", "searchPlaceholder": "Search records..." }
        });
    });

    // Your Exact Chart.js Initialization
    var ctx=document.getElementById('wasteChart');
    new Chart(ctx,{
        type:'bar',
        data:{
            labels:['Dry Waste','Wet Waste','E-Waste'],
            datasets:[{
                label: 'Kilograms (kg)',
                data:[<?php echo $dry ?>, <?php echo $wet ?>, <?php echo $ewaste ?>],
                backgroundColor:['#f59e0b','#10b981','#8b5cf6'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Upgraded Bootstrap 5 Dark Mode Toggle
    function toggleDarkMode(){
        let html = document.documentElement;
        let currentTheme = html.getAttribute("data-bs-theme");
        let newTheme = currentTheme === "dark" ? "light" : "dark";
        
        html.setAttribute("data-bs-theme", newTheme);
        localStorage.setItem("theme", newTheme);
    }

    // Load Dark Mode Preference on page load
    if(localStorage.getItem("theme") === "dark"){
        document.documentElement.setAttribute("data-bs-theme", "dark");
    }
</script>

</body>
</html>