<?php
require_once 'config/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read form values and escape for DB safety.
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert new normal user record.
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";
    if (mysqli_query($conn, $sql)) {
        $message = 'Registration successful. Please login.';
    } else {
        $message = 'Registration failed: ' . mysqli_error($conn);
    }
}
include 'includes/header.php';
?>
<h2>User Registration</h2>
<?php if($message): ?><div class="alert alert-info"><?php echo $message; ?></div><?php endif; ?>
<form method="post" class="card p-3">
    <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
    <button class="btn btn-primary">Register</button>
</form>
<?php include 'includes/footer.php'; ?>
