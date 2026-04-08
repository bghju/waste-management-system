<?php
session_start();

$conn = mysqli_connect("localhost","root","","waste_db");

$error="";

if(isset($_POST['login'])){

$username = $_POST['username'];
$password = $_POST['password'];
$query = "SELECT * FROM users 
WHERE username='$username' 
AND password='$password' 
AND role='admin'";
$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result) > 0){

$_SESSION['admin'] = $username;

header("Location: admin_dashboard.php");
exit();

}else{

$error="Invalid Login";

}

}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="login-page">


<div class="login-box">


<h2>Admin Login</h2>

<?php if($error!=""){ ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php } ?>

<form method="post">

<input type="text" name="username" placeholder="Username" required><br><br>

<input type="password" name="password" placeholder="Password" required><br><br>

<button type="submit" name="login">Login</button>

</form>

</div>

</body>
</html>
