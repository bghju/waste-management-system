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

$success_message = false;

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $block_name = $_POST['block_name'];
    $status = $_POST['campus_status'];
    
    // Notice these are now ARRAYS because of the [] in the HTML form
    $categories = $_POST['category']; 
    $quantities = $_POST['quantity']; 
    
    // Save exact date and time
    $date = date("Y-m-d H:i:s"); 

    // 1. Insert into main waste table ONCE
    $query1 = "INSERT INTO waste (date, block_name, campus_status) VALUES ('$date', '$block_name', '$status')";
    if (mysqli_query($conn, $query1)) {
        
        // Get the ID of the waste record we just created
        $waste_id = mysqli_insert_id($conn);

        // 2. Loop through every waste category the user added and save it to the details table
        for ($i = 0; $i < count($categories); $i++) {
            $cat = mysqli_real_escape_string($conn, $categories[$i]);
            $qty = mysqli_real_escape_string($conn, $quantities[$i]);
            
            // Only insert if they actually typed a category and quantity
            if (!empty($cat) && !empty($qty)) {
                $query2 = "INSERT INTO waste_details (waste_id, category, quantity) VALUES ('$waste_id', '$cat', '$qty')";
                mysqli_query($conn, $query2);
            }
        }
        $success_message = true; // Trigger the SweetAlert
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Waste | Pro Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

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
            background-color: #1e293b;
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
        .main-content { margin-left: 260px; padding: 30px; }
        .page-title { color: #0f172a; font-weight: 700; margin-bottom: 25px; }

        /* Premium Form Card */
        .custom-card {
            background: #ffffff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            padding: 35px;
            max-width: 800px;
        }

        .form-label { font-weight: 600; color: #475569; margin-bottom: 8px; }
        .form-control, .form-select {
            border-radius: 8px; padding: 12px 15px; border: 1px solid #cbd5e1; background-color: #f8fafc; transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); background-color: #ffffff;
        }
        
        /* Buttons */
        .btn-submit {
            background-color: #10b981; color: white; font-weight: 600; padding: 12px 25px; border-radius: 8px; border: none; transition: 0.3s; width: 100%; margin-top: 25px;
        }
        .btn-submit:hover { background-color: #059669; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); }
        
        .btn-add-more {
            background-color: #eff6ff; color: #3b82f6; border: 1px dashed #93c5fd; padding: 10px; border-radius: 8px; font-weight: 600; width: 100%; transition: 0.3s; margin-top: 15px;
        }
        .btn-add-more:hover { background-color: #dbeafe; }

        .waste-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-leaf"></i> EcoCampus</div>
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="add_waste.php" class="active"><i class="fa-solid fa-plus-circle"></i> Log Waste</a>
    <a href="view_waste.php"><i class="fa-solid fa-chart-line"></i> Waste Report</a>
    <a href="logout.php" style="margin-top: 50px; color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <h3 class="page-title"><i class="fa-solid fa-clipboard-list text-primary me-2"></i> Log Multiple Wastes</h3>

    <div class="custom-card">
        <form method="POST" action="">
            <div class="row g-4">
                
                <div class="col-md-6">
                    <label class="form-label"><i class="fa-regular fa-building me-1"></i> Block Name</label>
                    <select name="block_name" class="form-select" required>
                        <option value="" disabled selected>Select Block...</option>
                        <option value="AS Block">AS Block</option>
                        <option value="SF Block">SF Block</option>
                        <option value="Main Block">IB Block</option>
                        <option value="Hostel">Aero Block</option>
                        <option value="Canteen">Mech Block</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fa-solid fa-location-dot me-1"></i> Campus Status</label>
                    <select name="campus_status" class="form-select" required>
                        <option value="Inside" selected>Inside Campus</option>
                        <option value="Out">Mark as Out (Removed)</option>
                    </select>
                </div>
            </div>

            <div class="waste-section">
                <h6 class="mb-3 text-secondary fw-bold">Waste Breakdown</h6>
                <div id="waste-container">
                    <div class="row g-3 waste-row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Waste Category</label>
                            <select name="category[]" class="form-select" required>
                                <option value="" disabled selected>Select Category...</option>
                                <option value="Dry Waste">Dry Waste</option>
                                <option value="Wet Waste">Wet Waste</option>
                                <option value="E-Waste">E-Waste</option>
                                <option value="Hazardous">Hazardous</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted small mb-1">Quantity (kg)</label>
                            <input type="number" step="0.01" name="quantity[]" class="form-control" placeholder="e.g., 15.5" required>
                        </div>
                        <div class="col-md-1">
                            </div>
                    </div>
                </div>

                <button type="button" class="btn-add-more" id="add-waste-btn">
                    <i class="fa-solid fa-plus me-1"></i> Add Another Waste Type
                </button>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-check-circle me-1"></i> Save All Records
            </button>

        </form>
    </div>
</div>

<script>
    document.getElementById('add-waste-btn').addEventListener('click', function() {
        const container = document.getElementById('waste-container');
        
        // Create a new row
        const newRow = document.createElement('div');
        newRow.className = 'row g-3 waste-row align-items-end mt-2';
        
        newRow.innerHTML = `
            <div class="col-md-6">
                <select name="category[]" class="form-select" required>
                    <option value="" disabled selected>Select Category...</option>
                    <option value="Dry Waste">Dry Waste</option>
                    <option value="Wet Waste">Wet Waste</option>
                    <option value="E-Waste">E-Waste</option>
                    <option value="Hazardous">Hazardous</option>
                </select>
            </div>
            <div class="col-md-5">
                <input type="number" step="0.01" name="quantity[]" class="form-control" placeholder="e.g., 5.0" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger w-100 remove-btn" style="height: 48px; border-radius: 8px;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(newRow);
    });

    // Remove row logic using Event Delegation
    document.getElementById('waste-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-btn')) {
            e.target.closest('.waste-row').remove();
        }
    });
</script>

<?php if ($success_message): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Successfully Logged!',
        text: 'All waste types have been saved.',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = 'view_waste.php';
    });
</script>
<?php endif; ?>

</body>
</html>