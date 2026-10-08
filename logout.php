<?php
// logout.php
session_start();
$_SESSION = [];
session_destroy();

header("Location: login.php?msg=logout");
exit;