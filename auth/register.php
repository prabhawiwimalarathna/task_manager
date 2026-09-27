<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (current_user()) {
    header('Location: ../dashboard.php');
    exit;
}

$errors = [];
$username = '';
$email = '';
$loginError = flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your form session expired. Please try again.';
    }
    if (!preg_match('/^[\p{L}\p{N}_. -]{2,50}$/u', $username)) {
        $errors[] = 'Username must be 2-50 characters and use letters, numbers, spaces, dots, underscores, or hyphens.';
    }
    if (!valid_email($email)) {
        $errors[] = 'Enter a valid email address (maximum 100 characters).';
    }
    if (strlen($password) < 8 || strlen($password) > 72) {
        $errors[] = 'Password must be between 8 and 72 characters.';
    }

    if (!$errors) {
        try {
            $statement = get_db()->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
            $statement->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int) get_db()->lastInsertId(), 'username' => $username, 'email' => $email];
            header('Location: ../dashboard.php');
            exit;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'That email address is already registered. Try logging in instead.';
            } else {
                error_log($exception->getMessage());
                $errors[] = 'We could not create your account just now. Check the database setup and try again.';
            }
        }
    }
}

$pageTitle = 'Create your account';
require __DIR__ . '/../includes/header.php';
?>
<section class="container auth-layout">
    <div class="auth-copy"><span class="eyebrow">A good place to begin</span><h1>Make space for the work that matters.</h1><p>Create a free account to gather your tasks, sort the day, and keep track of the progress you make.</p></div>
    <div class="form-panel">
        <h2 class="h4 mb-1">Create your account</h2><p class="text-secondary mb-4">It only takes a moment.</p>
        <?php if ($loginError): ?><div class="alert alert-info" role="status"><?= e($loginError) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-danger" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" data-validate novalidate>
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" value="<?= e($username) ?>" minlength="2" maxlength="50" autocomplete="username" required><div class="invalid-feedback">Enter a username with at least 2 characters.</div></div>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" maxlength="100" autocomplete="email" required><div class="invalid-feedback">Enter a valid email address.</div></div>
            <div class="mb-4"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required><div class="form-text">Use at least 8 characters.</div><div class="invalid-feedback">Use at least 8 characters.</div></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Create account</button>
        </form>
        <p class="mt-4 mb-0 text-secondary">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>