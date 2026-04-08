<?php
$conn = mysqli_connect("localhost","root","","waste_db");

$result = mysqli_query($conn,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<h2>Manage Users</h2>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Username</th>
<th>Role</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)) { ?>

<tr>

<td><?= $row['id'] ?></td>
<td><?= $row['username'] ?></td>
<td><?= $row['role'] ?></td>

<td>
<?php
if($row['status']=="blocked")
echo "<span style='color:red'>Blocked</span>";
else
echo "<span style='color:green'>Active</span>";
?>
</td>

<td>

<a href="block_user.php?id=<?= $row['id'] ?>">Block</a> |

<a href="delete_user.php?id=<?= $row['id'] ?>"
onclick="return confirm('Delete this user?')">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

</body>
</html>