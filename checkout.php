<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $user_id = (int)$_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $total = 0;

    foreach($_SESSION['cart'] as $food_id => $qty) {
        $res = mysqli_query($conn, "SELECT price FROM foods WHERE id=".(int)$food_id);
        $f = mysqli_fetch_assoc($res);
        if ($f) $total += $f['price'] * $qty;
    }

    // Save order master row.
    $sql = "INSERT INTO orders (user_id, total_amount, address, phone) VALUES ($user_id, $total, '$address', '$phone')";
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);

        // Save each cart item into order_items table.
        foreach($_SESSION['cart'] as $food_id => $qty) {
            $res = mysqli_query($conn, "SELECT price FROM foods WHERE id=".(int)$food_id);
            $f = mysqli_fetch_assoc($res);
            if ($f) {
                $price = $f['price'];
                mysqli_query($conn, "INSERT INTO order_items (order_id, food_id, quantity, price) VALUES ($order_id, $food_id, $qty, $price)");
            }
        }
        unset($_SESSION['cart']);
        $message = 'Order placed successfully!';
    } else {
        $message = 'Order failed: ' . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>
<h2>Checkout</h2>
<?php if($message): ?><div class="alert alert-info"><?php echo $message; ?></div><?php endif; ?>
<form method="post" class="card p-3">
    <textarea name="address" class="form-control mb-2" placeholder="Delivery Address" required></textarea>
    <input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
    <button class="btn btn-primary">Place Order</button>
</form>
<?php include 'includes/footer.php'; ?>
