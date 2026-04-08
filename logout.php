<?php
session_start();

/* destroy session */
session_destroy();

/* redirect to home page */
header("Location: index.php");
exit();
?>