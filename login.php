<?php
require_once 'config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Find user/admin by email.
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    $user = mysqli_fetch_assoc($result);

    // Validate password hash then create correct session keys.
    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] === 'admin') {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            header('Location: admin/dashboard.php');
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: index.php');
        }
        exit;
    } else {
        $message = 'Invalid email or password.';
    }
}
include 'includes/header.php';
?>
<h2>Login</h2>
<?php if($message): ?><div class="alert alert-danger"><?php echo $message; ?></div><?php endif; ?>
<form method="post" class="card p-3">
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
    <button class="btn btn-success">Login</button>
</form>
<?php include 'includes/footer.php'; ?>
