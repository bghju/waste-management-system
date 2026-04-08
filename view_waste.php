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

$result = mysqli_query($conn,
"SELECT w.id, w.date, w.block_name, w.campus_status,
GROUP_CONCAT(CONCAT(d.category, ': ', d.quantity, ' kg') SEPARATOR ', ') AS waste_details
FROM waste w
JOIN waste_details d ON w.id = d.waste_id
GROUP BY w.id
ORDER BY w.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Report | Pro Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            background-color: #1e293b; /* Premium Dark Slate */
            padding-top: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-brand {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }
        .sidebar-brand i { color: #10b981; } /* Emerald Green Icon */
        
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 15px;
            color: #94a3b8;
            display: block;
            transition: 0.3s;
            font-weight: 500;
        }
        .sidebar a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #ffffff;
            background-color: #334155;
            border-left: 4px solid #10b981;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .page-title {
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Premium Card Design */
        .custom-card {
            background: #ffffff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            padding: 25px;
        }

        /* Status Badges */
        .badge-inside { background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 6px 12px; border-radius: 20px; font-weight: 600; }
        .badge-out { background-color: #f0fdf4; color: #10b981; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 20px; font-weight: 600; }

        /* Action Buttons */
        .btn-action { width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; border-radius: 6px; margin: 0 3px; border: none; transition: 0.2s; }
        .btn-markout { background: #fffbeb; color: #f59e0b; border: 1px solid #fde68a; }
        .btn-markout:hover { background: #f59e0b; color: #fff; }
        .btn-edit { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
        .btn-edit:hover { background: #3b82f6; color: #fff; }
        .btn-delete { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .btn-delete:hover { background: #ef4444; color: #fff; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-leaf"></i> EcoCampus
    </div>
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="add_waste.php"><i class="fa-solid fa-plus-circle"></i> Log Waste</a>
    <a href="view_waste.php" class="active"><i class="fa-solid fa-chart-line"></i> Waste Report</a>
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <h3 class="page-title"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Monthly Waste Report</h3>

    <div class="custom-card">
        <div class="table-responsive">
            <table id="wasteTable" class="table table-hover align-middle" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Block</th>
                        <th>Waste Details</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { 
                    ?>
                    <tr>
                        <td class="text-muted fw-medium">
                            <i class="fa-regular fa-clock me-1"></i> <?php echo date('d-M-Y h:i A', strtotime($row['date'])); ?>
                        </td>
                        <td class="fw-bold text-dark"><?php echo $row['block_name']; ?></td>
                        <td><?php echo $row['waste_details']; ?></td>
                        <td>
                            <?php if ($row['campus_status'] == 'Inside') { ?>
                                <span class="badge-inside"><i class="fa-solid fa-box-archive me-1"></i> Inside</span>
                            <?php } else { ?>
                                <span class="badge-out"><i class="fa-solid fa-truck-fast me-1"></i> Out</span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['campus_status'] == 'Inside') { ?>
                                <button onclick="confirmAction('update_status.php?id=<?php echo $row['id']; ?>', 'Mark this waste as Out?', 'This means the truck has taken the waste away.', 'Yes, mark it Out!', '#f59e0b')" class="btn-action btn-markout" title="Mark Out">
                                    <i class="fa-solid fa-truck"></i>
                                </button>
                            <?php } ?>

                            <a href="edit_waste.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit d-inline-block" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            
                            <button onclick="confirmAction('delete_waste.php?id=<?php echo $row['id']; ?>&from=user', 'Are you sure?', 'You cannot recover this!', 'Yes, delete it!', '#ef4444')" class="btn-action btn-delete">
    <i class="fa-solid fa-trash"></i>
</button>
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
</div>

<script>
    $(document).ready(function() {
        $('#wasteTable').DataTable({
            "pageLength": 10,
            "order": [], // Keeps your SQL exact order (newest at the top)
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search records..."
            }
        });
    });

    function confirmAction(url, title, text, confirmButtonText, confirmButtonColor) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#94a3b8',
            confirmButtonText: confirmButtonText,
            customClass: {
                confirmButton: 'btn btn-primary ms-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
</script>

</body>
</html>