<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awareness | EcoCampus</title>

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

        .main-content { margin-left: 260px; padding: 40px; }

        /* Page Header */
        .page-title { color: #0f172a; font-weight: 700; margin-bottom: 10px; }
        .page-subtitle { color: #64748b; font-size: 16px; margin-bottom: 40px; }

        /* Waste Guide Cards */
        .guide-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px 25px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            border-bottom: 5px solid transparent;
        }
        .guide-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1); }
        
        .guide-card.dry { border-bottom-color: #f97316; }
        .guide-card.wet { border-bottom-color: #10b981; }
        .guide-card.ewaste { border-bottom-color: #ef4444; }

        .guide-icon {
            font-size: 40px; margin-bottom: 20px;
        }
        .guide-card.dry .guide-icon { color: #f97316; }
        .guide-card.wet .guide-icon { color: #10b981; }
        .guide-card.ewaste .guide-icon { color: #ef4444; }

        .guide-card h4 { font-weight: 700; color: #1e293b; margin-bottom: 15px; }
        .guide-card p { color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        
        .guide-list { padding-left: 0; list-style: none; margin-bottom: 0; }
        .guide-list li { font-size: 14px; color: #64748b; margin-bottom: 8px; font-weight: 500; }
        .guide-list li i { margin-right: 8px; font-size: 12px; }

        .guide-card.dry .guide-list li i { color: #f97316; }
        .guide-card.wet .guide-list li i { color: #10b981; }
        .guide-card.ewaste .guide-list li i { color: #ef4444; }

        /* Action Banner */
        .action-banner {
            background: #1e293b;
            border-radius: 12px;
            padding: 30px;
            color: #ffffff;
            margin-top: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .action-text h4 { font-weight: 700; color: #3b82f6; margin-bottom: 10px; }
        .action-text p { margin-bottom: 0; color: #94a3b8; font-size: 15px; }
        .action-icon { font-size: 50px; color: #334155; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i> Student Portal</div>
    
    <a href="student_dashboard.php"><i class="fa-solid fa-house-user"></i> Dashboard</a>
    <a href="student_view.php"><i class="fa-solid fa-chart-pie"></i> Full Report</a>
    <a href="awareness.php" class="active"><i class="fa-solid fa-lightbulb"></i> Awareness</a>
</div>

<div class="main-content">
    
    <h2 class="page-title"><i class="fa-solid fa-leaf text-success me-2"></i> Campus Environmental Guidelines</h2>
    <p class="page-subtitle">Learn how to properly segregate waste to keep our campus clean and sustainable.</p>

    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="guide-card dry">
                <div class="guide-icon"><i class="fa-solid fa-box-open"></i></div>
                <h4>Dry Waste</h4>
                <p>Dry waste consists of materials that do not decay or decompose easily. They should be kept clean and dry before disposal.</p>
                <ul class="guide-list">
                    <li><i class="fa-solid fa-circle-check"></i> Paper & Cardboard</li>
                    <li><i class="fa-solid fa-circle-check"></i> Plastic Bottles & Wrappers</li>
                    <li><i class="fa-solid fa-circle-check"></i> Glass & Metals</li>
                    <li><i class="fa-solid fa-circle-check"></i> Clean cloth & rubber</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="guide-card wet">
                <div class="guide-icon"><i class="fa-solid fa-apple-whole"></i></div>
                <h4>Wet Waste</h4>
                <p>Wet waste is biodegradable organic matter that can be composted. Do not mix this with plastics or glass.</p>
                <ul class="guide-list">
                    <li><i class="fa-solid fa-circle-check"></i> Leftover Food</li>
                    <li><i class="fa-solid fa-circle-check"></i> Fruit & Vegetable Peels</li>
                    <li><i class="fa-solid fa-circle-check"></i> Tea leaves & Coffee grounds</li>
                    <li><i class="fa-solid fa-circle-check"></i> Garden waste (leaves, twigs)</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="guide-card ewaste">
                <div class="guide-icon"><i class="fa-solid fa-plug-circle-bolt"></i></div>
                <h4>E-Waste</h4>
                <p>Electronic waste contains toxic materials that can harm the environment. They must be handled carefully by professionals.</p>
                <ul class="guide-list">
                    <li><i class="fa-solid fa-circle-check"></i> Dead Batteries</li>
                    <li><i class="fa-solid fa-circle-check"></i> Broken Chargers & Cables</li>
                    <li><i class="fa-solid fa-circle-check"></i> Old Laptops & Phones</li>
                    <li><i class="fa-solid fa-circle-check"></i> Circuit boards & CDs</li>
                </ul>
            </div>
        </div>

    </div>

    <div class="action-banner">
        <div class="action-text">
            <h4><i class="fa-solid fa-hands-holding-circle me-2"></i> Be the Change!</h4>
            <p>Every small step counts. Segregating waste at the source reduces landfill pressure and allows us to recycle efficiently.</p>
        </div>
        <div class="action-icon">
            <i class="fa-solid fa-recycle"></i>
        </div>
    </div>

</div>

</body>
</html>