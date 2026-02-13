<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "waste_db");

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['username'] = $username;
        header("Location: add_waste.php");
        exit();
    } else {
        $error = "❌ Invalid Username or Password";
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

    <div class="login-container">
        <h2>♻ Academic Waste Management</h2>
        <p class="subtitle">Admin Login</p>

        <?php if ($error != "") { ?>
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
