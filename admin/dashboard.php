<?php
require_once '../config/db.php';
require_once '../includes/admin_auth.php';

$foods_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM foods"))['c'];
$orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2>Admin Panel</h2>
  <a href="foods.php" class="btn btn-primary my-3">Manage Foods</a>
  <a href="../logout.php" class="btn btn-secondary my-3">Logout</a>
  <div class="row">
    <div class="col-md-6"><div class="card p-3"><h4>Total Foods: <?php echo $foods_count; ?></h4></div></div>
    <div class="col-md-6"><div class="card p-3"><h4>Total Orders: <?php echo $orders_count; ?></h4></div></div>
  </div>
</div>
</body>
</html>
