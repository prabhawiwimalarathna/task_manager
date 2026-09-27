<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (current_user()) {
    header('Location: ../dashboard.php');
    exit;
}

$error = flash('error');
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your form session expired. Please try again.';
    } elseif (!valid_email($email) || $password === '') {
        $error = 'Enter a valid email address and password.';
    } else {
        try {
            $statement = get_db()->prepare('SELECT id, username, email, password FROM users WHERE email = :email LIMIT 1');
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int) $user['id'], 'username' => $user['username'], 'email' => $user['email']];
                header('Location: ../dashboard.php');
                exit;
            }
            $error = 'Email or password was not recognized.';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $error = 'We could not connect to the database. Check the setup instructions and try again.';
        }
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/../includes/header.php';
?>
<section class="container auth-layout">
    <div class="auth-copy"><span class="eyebrow">Welcome back</span><h1>Pick up where you left off.</h1><p>Your next clear step is waiting. Log in to see your task list and the progress you have already made.</p></div>
    <div class="form-panel">
        <h2 class="h4 mb-1">Log in to Daymark</h2><p class="text-secondary mb-4">Use the email connected to your account.</p>
        <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" data-validate novalidate>
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" autocomplete="email" required><div class="invalid-feedback">Enter a valid email address.</div></div>
            <div class="mb-4"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="current-password" required><div class="invalid-feedback">Enter your password (at least 8 characters).</div></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
        </form>
        <p class="mt-4 mb-0 text-secondary">New to Daymark? <a href="register.php">Create an account</a></p>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>