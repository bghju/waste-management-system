<?php
session_start();

$conn = mysqli_connect("localhost","root","","waste_db");

$error="";

if(isset($_POST['login']))
{
$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users 
          WHERE username='$username' 
          AND password='$password' 
          AND role='user'";

$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result) > 0)
{
    $_SESSION['user'] = $username;

    // redirect to home page
    header("Location: index.php");
    exit();
}
else
{
    $error = "❌ Invalid Username or Password";
}
}
?>

<!DOCTYPE html>
<html>
<head>

<title>User Login</title>
<link rel="stylesheet" href="style.css">

<style>

body{
font-family:Arial;
background:#f4f6f9;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
margin:0;
}

.login-container{
background:white;
padding:30px;
width:350px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
text-align:center;
}

.login-container h2{
margin-bottom:5px;
}

.subtitle{
color:#777;
margin-bottom:20px;
}

input{
width:100%;
padding:10px;
margin:8px 0;
border:1px solid #ccc;
border-radius:5px;
}

button{
width:100%;
padding:10px;
background:#2c6faa;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
font-size:16px;
}

button:hover{
background:#1f4f7a;
}

.error-msg{
background:#ffe0e0;
color:red;
padding:8px;
margin-bottom:10px;
border-radius:5px;
}

</style>

</head>

<body>

<div class="login-container">

<h2>♻ Academic Waste Management</h2>
<p class="subtitle">User Login</p>

<?php if($error!=""){ ?>
<div class="error-msg"><?php echo $error; ?></div>
<?php } ?>

<form method="post">

<input type="text" name="username" placeholder="Enter Username" required>

<input type="password" name="password" placeholder="Enter Password" required>

<button type="submit" name="login">Login</button>

</form>

</div>

</body>
</html>