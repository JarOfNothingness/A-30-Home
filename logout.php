<?php
session_start();
session_unset();
session_destroy();

// Clear cookies


header("Location: login.php");
exit();
?>
