<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$conn = mysqli_connect("localhost", "root", "", "waste_db");
$result = mysqli_query($conn, "SELECT * FROM waste");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Waste Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header"><h2>Waste Report</h2></div>

<div class="nav">
    <a href="add_waste.php">Add Waste</a>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
<table>
<tr><th>Date</th><th>Category</th><th>Quantity</th><th>Campus Status</th>
<th>Action</th>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['date'] ?></td>
    <td><?= $row['category'] ?></td>
    <td><?= $row['quantity'] ?></td>

    <?php if ($row['campus_status'] == 'Inside') { ?>
        <td style="color:red;font-weight:bold;">Inside</td>
        <td>
            <a href="update_status.php?id=<?= $row['id'] ?>">Mark Out</a>
        </td>
    <?php } else { ?>
        <td style="color:green;font-weight:bold;">Out</td>
        <td>—</td>
    <?php } ?>

</tr>
<?php } ?>

</table>
</div>

</body>
</html>
