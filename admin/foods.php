<?php
require_once '../config/db.php';
require_once '../includes/admin_auth.php';

// Add new food.
if (isset($_POST['add_food'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    mysqli_query($conn, "INSERT INTO foods(name, description, price, image) VALUES('$name','$description',$price,'$image')");
}

// Delete food.
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM foods WHERE id=$id");
}

// Update food.
if (isset($_POST['update_food'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    mysqli_query($conn, "UPDATE foods SET name='$name', description='$description', price=$price, image='$image' WHERE id=$id");
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM foods WHERE id=$id");
    $edit_item = mysqli_fetch_assoc($res);
}

$foods = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");
?>
<!DOCTYPE html><html><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Foods</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container py-4">
<h2>Manage Foods</h2>
<a href="dashboard.php" class="btn btn-secondary mb-3">Back</a>
<form method="post" class="card p-3 mb-3">
    <input type="hidden" name="id" value="<?php echo $edit_item['id'] ?? ''; ?>">
    <input class="form-control mb-2" name="name" placeholder="Food Name" value="<?php echo $edit_item['name'] ?? ''; ?>" required>
    <textarea class="form-control mb-2" name="description" placeholder="Description" required><?php echo $edit_item['description'] ?? ''; ?></textarea>
    <input class="form-control mb-2" type="number" step="0.01" name="price" placeholder="Price" value="<?php echo $edit_item['price'] ?? ''; ?>" required>
    <input class="form-control mb-2" name="image" placeholder="Image URL or path" value="<?php echo $edit_item['image'] ?? ''; ?>" required>
    <?php if($edit_item): ?>
        <button class="btn btn-warning" name="update_food">Update Food</button>
    <?php else: ?>
        <button class="btn btn-primary" name="add_food">Add Food</button>
    <?php endif; ?>
</form>
<table class="table table-bordered">
<tr><th>ID</th><th>Name</th><th>Price</th><th>Action</th></tr>
<?php while($food = mysqli_fetch_assoc($foods)): ?>
<tr>
<td><?php echo $food['id']; ?></td><td><?php echo $food['name']; ?></td><td>$<?php echo $food['price']; ?></td>
<td>
<a class="btn btn-sm btn-info" href="foods.php?edit=<?php echo $food['id']; ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="foods.php?delete=<?php echo $food['id']; ?>" onclick="return confirm('Delete this food?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div></body></html>
