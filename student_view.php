<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student View</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header"><h2>Student Waste View</h2></div>

<div class="container">
<table>
<tr><th>Date</th><th>Category</th><th>Quantity</th><th>Campus Status</th></tr>
<?php
$conn = mysqli_connect("localhost", "root", "", "waste_db");
$res = mysqli_query($conn, "SELECT * FROM waste");
while ($r = mysqli_fetch_assoc($res)) {
    echo "<tr><td>{$r['date']}</td><td>{$r['category']}</td><td>{$r['quantity']}</td><td>{$r['campus_status']}</td>
</tr>";
}
?>
</table>

<p><a href="awareness.php">Waste Awareness</a></p>
</div>

</body>
</html>
