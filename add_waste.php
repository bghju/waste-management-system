<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$conn = mysqli_connect("localhost", "root", "", "waste_db");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Waste</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header"><h2>Add Waste</h2></div>

<div class="nav">
    <a href="add_waste.php">Add Waste</a>
    <a href="view_waste.php">View Report</a>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
<form method="post">
    <label>Campus Status:</label>
<select name="campus_status" required>
    <option value="">Select</option>
    <option value="Inside">Inside Campus</option>
    <option value="Out">Out of Campus</option>
</select>

    Date:
    <input type="date" name="date" required>

    Waste Type:
    <select name="type" required>
        <option value="">Select</option>
        <option>Dry</option>
        <option>Wet</option>
        <option>E-Waste</option>
    </select>

    Quantity (kg):
    <input type="number" name="quantity" required>

    <input type="submit" name="submit" value="Save">
</form>

<?php
if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $campus_status = $_POST['campus_status'];

    mysqli_query($conn, "INSERT INTO waste (date, category, quantity, campus_status)
VALUES ('$date', '$type', '$quantity', '$campus_status')");
    echo "<p>Waste saved!</p>";
}
?>
</div>

</body>
</html>
