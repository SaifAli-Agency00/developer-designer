<?php
require_once 'config/db.php';

// Handle add-to-cart action from menu cards.
if (isset($_POST['add_to_cart'])) {
    $food_id = (int)$_POST['food_id'];
    $qty = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$food_id] = ($_SESSION['cart'][$food_id] ?? 0) + $qty;
}

$foods = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");
include 'includes/header.php';
?>
<h2 class="mb-3">Food Menu</h2>
<div class="row">
<?php while($food = mysqli_fetch_assoc($foods)): ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <img src="<?php echo $food['image']; ?>" class="card-img-top" alt="food image">
            <div class="card-body">
                <h5><?php echo $food['name']; ?></h5>
                <p><?php echo $food['description']; ?></p>
                <strong>$<?php echo number_format($food['price'], 2); ?></strong>
                <form method="post" class="mt-2 d-flex gap-2">
                    <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                    <input type="number" name="quantity" class="form-control" value="1" min="1">
                    <button class="btn btn-primary" name="add_to_cart">Add</button>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
