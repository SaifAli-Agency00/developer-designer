<?php
require_once 'config/db.php';

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
}

include 'includes/header.php';
$total = 0;
?>
<h2>Your Cart</h2>
<?php if(empty($_SESSION['cart'])): ?>
    <div class="alert alert-warning">Cart is empty.</div>
<?php else: ?>
<table class="table table-bordered">
    <tr><th>Food</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
    <?php foreach($_SESSION['cart'] as $food_id => $qty):
        $res = mysqli_query($conn, "SELECT * FROM foods WHERE id=".(int)$food_id);
        $food = mysqli_fetch_assoc($res);
        if (!$food) continue;
        $sub = $food['price'] * $qty;
        $total += $sub;
    ?>
    <tr>
        <td><?php echo $food['name']; ?></td>
        <td>$<?php echo number_format($food['price'],2); ?></td>
        <td><?php echo $qty; ?></td>
        <td>$<?php echo number_format($sub,2); ?></td>
        <td><a class="btn btn-sm btn-danger" href="cart.php?remove=<?php echo $food_id; ?>">Remove</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<h4>Total: $<?php echo number_format($total,2); ?></h4>
<a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
