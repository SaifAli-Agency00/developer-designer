<?php
// Require user login for protected pages.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
