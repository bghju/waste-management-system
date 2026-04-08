<?php
session_start();

if(!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","waste_db");
if (!$conn) die("Database Connection Failed");

// Get date range from URL, default to the last 7 days
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-7 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

/* GET TOTALS FOR THE SELECTED DATE RANGE */
$q_total = mysqli_query($conn, "SELECT SUM(d.quantity) as t FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE DATE(w.date) BETWEEN '$from_date' AND '$to_date'");
$total = mysqli_fetch_assoc($q_total)['t'] ?? 0;

$q_dry = mysqli_query($conn, "SELECT SUM(d.quantity) as d_val FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE d.category='Dry Waste' AND DATE(w.date) BETWEEN '$from_date' AND '$to_date'");
$dry = mysqli_fetch_assoc($q_dry)['d_val'] ?? 0;

$q_wet = mysqli_query($conn, "SELECT SUM(d.quantity) as w_val FROM waste w JOIN waste_details d ON w.id = d.waste_id WHERE d.category='Wet Waste' AND DATE(w.date) BETWEEN '$from_date' AND '$to_date'");
$wet = mysqli_fetch_assoc($q_wet)['w_val'] ?? 0;

/* GET DETAILED LOGS FOR THE SELECTED DATE RANGE */
$query = "
    SELECT w.id, w.date, w.block_name, w.campus_status,
    GROUP_CONCAT(CONCAT(d.category,': ',d.quantity,' kg') SEPARATOR '<br>') AS waste_details
    FROM waste w
    JOIN waste_details d ON w.id = d.waste_id
    WHERE DATE(w.date) BETWEEN '$from_date' AND '$to_date'
    GROUP BY w.id 
    ORDER BY w.date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Reports | EcoCampus Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: var(--bs-body-bg); overflow-x: hidden; transition: 0.3s; }

        /* ADMIN SIDEBAR */
        .sidebar { height: 100vh; width: 260px; position: fixed; top: 0; left: 0; background-color: #111827; padding-top: 20px; box-shadow: 4px 0 15px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar-brand { color: #fff; font-size: 20px; font-weight: 800; text-align: center; margin-bottom: 30px; letter-spacing: 1px; }
        .sidebar-brand i { color: #10b981; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 15px; color: #9ca3af; display: block; transition: 0.3s; font-weight: 500; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { color: #ffffff; background-color: #1f2937; border-left: 4px solid #10b981; }

        .main-content { margin-left: 260px; padding: 30px; }

        /* Custom UI Elements */
        .admin-header { background: var(--bs-body-bg); padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); margin-bottom: 20px; border: 1px solid var(--bs-border-color); display: flex; justify-content: space-between; align-items: center; }
        .custom-card { background: var(--bs-body-bg); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; border: 1px solid var(--bs-border-color); margin-bottom: 25px; }
        
        .filter-box { background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        
        .stat-badge { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 15px 20px; border-radius: 8px; text-align: center; }
        [data-bs-theme="dark"] .stat-badge { background: #1f2937; border-color: #374151; }
        
        .stat-badge h6 { color: #6b7280; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; font-size: 12px; }
        .stat-badge h3 { font-weight: 800; margin: 0; color: #3b82f6; }

        @media print {
            .sidebar, .filter-box, .dataTables_wrapper .row:first-child, .dataTables_wrapper .row:last-child, .btn { display: none !important; }
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
    <a href="reports.php" class="active"><i class="fa fa-file"></i> Reports</a>
    <a href="monthly_report.php"><i class="fa fa-chart-pie"></i> Monthly Report</a>
</div>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h4 class="mb-0"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Custom Date Reports</h4>
            <span class="text-muted" style="font-size: 14px;">Generate records for specific timeframes</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-dark"><i class="fa-solid fa-print me-2"></i> Print</button>
            
            <button id="pdf-btn" onclick="generatePDF()" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf me-2"></i> Download PDF
            </button>
        </div>
    </div>

    <div class="filter-box">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted small">From Date</label>
                <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted small">To Date</label>
                <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-2"></i> Generate Report</button>
            </div>
        </form>
    </div>

    <div id="pdf-content">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-badge">
                    <h6>Total Waste in Range</h6>
                    <h3><?php echo number_format($total, 1); ?> kg</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-badge">
                    <h6>Dry Waste</h6>
                    <h3 style="color: #f59e0b;"><?php echo number_format($dry, 1); ?> kg</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-badge">
                    <h6>Wet Waste</h6>
                    <h3 style="color: #10b981;"><?php echo number_format($wet, 1); ?> kg</h3>
                </div>
            </div>
        </div>

        <div class="custom-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-list me-2 text-secondary"></i> Report Data (<?php echo date('d-M-Y', strtotime($from_date)); ?> to <?php echo date('d-M-Y', strtotime($to_date)); ?>)</h5>
            
            <div class="table-responsive">
                <table id="reportTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Block Name</th>
                            <th>Waste Summary</th>
                            <th>Campus Status</th>
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
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> </div>

<script>
    // DataTables Initialization
    $(document).ready(function() {
        $('#reportTable').DataTable({
            "pageLength": 25,
            "order": [[ 0, "desc" ]],
            "language": { "search": "_INPUT_", "searchPlaceholder": "Filter these results..." }
        });
    });

    // Dark mode logic
    if(localStorage.getItem("theme") === "dark"){
        document.documentElement.setAttribute("data-bs-theme", "dark");
    }

    // NEW, SAFE PDF GENERATION FUNCTION
    function generatePDF() {
        // 1. Change button text so you know it's working
        var btn = document.getElementById('pdf-btn');
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Generating...';
        btn.disabled = true; // Prevent double clicking

        // 2. Target the content
        var element = document.getElementById('pdf-content');
        
        // 3. Setup PDF options (Landscape mode + forced width prevents crashes!)
        var opt = {
            margin:       0.3,
            filename:     'Waste_Report_<?php echo $from_date; ?>_to_<?php echo $to_date; ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, windowWidth: 1200 }, 
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' } 
        };

        // 4. Generate the PDF, then safely reset the button
        setTimeout(function() {
            html2pdf().set(opt).from(element).save().then(function() {
                // Once downloaded, put the button back to normal
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(function(error) {
                // If it fails, log it but don't freeze the page!
                console.error("PDF Error: ", error);
                alert("Something went wrong generating the PDF.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }, 500); // Small half-second delay to let the browser breathe
    }
</script>

</body>
</html>