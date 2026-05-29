<?php
// Require admin login for admin pages.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
