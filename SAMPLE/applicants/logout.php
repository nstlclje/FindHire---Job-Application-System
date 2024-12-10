<?php
session_start();
session_unset();
session_destroy();
header("Location: ../applicants/login.php");
exit();
?>