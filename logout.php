<?php
require_once 'config/db.php';
// Destroy entire session for logout of any role.
session_unset();
session_destroy();
header('Location: login.php');
exit;
?>
