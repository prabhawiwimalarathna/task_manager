<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = false;
$name = '';
$email = '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $message = trim((string) ($_POST['message'] ?? ''));
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your form session expired. Please reload the page and try again.';
    }
    if ($name === '' || strlen($name) > 100) {
        $errors[] = 'Name is required and must be at most 100 characters.';
    }
    if (!valid_email($email)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($message === '' || strlen($message) > 5000) {
        $errors[] = 'Message is required and must be at most 5,000 characters.';
    }
    if (!$errors) {
        try {
            $statement = get_db()->prepare('INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)');
            $statement->execute(['name' => $name, 'email' => $email, 'message' => $message]);
            $success = true;
            $name = $email = $message = '';
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $errors[] = 'Your message could not be sent. Check the database setup and try again.';
        }
    }
}

$pageTitle = 'Contact';
$currentPage = 'contact';
require __DIR__ . '/includes/header.php';
?>
<section class="container">
    <header class="page-intro"><span class="eyebrow">We read every note</span><h1>Say hello.</h1><p>Have a question about Daymark or a thought about the project? Send a note below.</p></header>
    <div class="contact-layout">
        <aside class="contact-note"><h2>Good things start with a conversation.</h2><p>Share what is on your mind and we will get back to you as soon as we can.</p><p class="small">Messages are stored securely in the project database for the site administrator.</p></aside>
        <div class="form-panel">
            <?php if ($success): ?><div class="alert alert-success" role="status">Thanks. Your message has been received.</div><?php endif; ?>
            <?php if ($errors): ?><div class="alert alert-danger" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="post" data-validate novalidate>
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="<?= e($name) ?>" maxlength="100" autocomplete="name" required><div class="invalid-feedback">Enter your name.</div></div>
                <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" maxlength="100" autocomplete="email" required><div class="invalid-feedback">Enter a valid email address.</div></div>
                <div class="mb-4"><label class="form-label" for="message">Message</label><textarea class="form-control" id="message" name="message" rows="6" maxlength="5000" required><?= e($message) ?></textarea><div class="invalid-feedback">Write a message before sending.</div></div>
                <button class="btn btn-primary px-4" type="submit">Send message <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>