<?php
require_once "./php/config.php";
require_once "./php/users.php";

logoutUser();
header("Location: index.php");
exit;
?>