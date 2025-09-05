<?php
session_start();
require_once "Core/dbconfig.php";
require_once "Classes/User.php";
require_once "Classes/Layout.php";

$user = new User($pdo);

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $user->login($_POST['username'], $_POST['password']);
    if ($u) {
        $_SESSION['user'] = $u;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

echo Layout::header("Login");
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="register.php" class="text-decoration-none">Don't have an account? Register</a>
            </div>
        </div>
    </div>
</div>

<?php
echo Layout::footer();
 