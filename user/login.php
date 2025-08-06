<?php
// Start session first
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// If user is already logged in, redirect to index (optional - remove if you want logged-in users to access login page)
// if (isset($_SESSION['user_id'])) {
//     header('Location: index.php');
//     exit;
// }

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_credential = trim($_POST['login_credential']);
    $password = $_POST['password'];

    $errors = [];

    if (empty($login_credential)) $errors[] = "Username or Email is required";
    if (empty($password)) $errors[] = "Password is required";

    if (empty($errors)) {
        // Check if input is email or username
        if (filter_var($login_credential, FILTER_VALIDATE_EMAIL)) {
            // Login with email
            $stmt = $pdo->prepare("SELECT id, user_name, name, email, password FROM users WHERE email = ?");
        } else {
            // Login with username
            $stmt = $pdo->prepare("SELECT id, user_name, name, email, password FROM users WHERE user_name = ?");
        }
        
        $stmt->execute([$login_credential]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['email'];
            $redirect = $_GET['redirect'] ?? 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            $errors[] = "Invalid username/email or password";
        }
    }
}

// Include header after all PHP logic that might redirect
require_once '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="booking-form">
                <h2 class="text-center mb-4">Login</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="login_credential" class="form-label">Username or Email Address</label>
                        <input type="text" class="form-control" id="login_credential" name="login_credential" 
                               value="<?= htmlspecialchars($_POST['login_credential'] ?? '') ?>" 
                               placeholder="Enter your username or email address" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="text-center mt-3">
                    <p><a href="forgot-password.php" class="text-decoration-none">Forgot your password?</a></p>
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
