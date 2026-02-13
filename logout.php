<?php
session_start();
session_unset();
session_destroy();

// Redirect to HOME page
header("Location: index.php");
exit();
?>
