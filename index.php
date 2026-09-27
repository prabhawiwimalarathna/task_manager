<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'A little more focus, every day';
$currentPage = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero-home" aria-labelledby="home-title">
    <div class="container">
        <div class="hero-content">
            <span class="eyebrow">Make room for what matters</span>
            <h1 id="home-title">A clear day starts with Daymark.</h1>
            <p>A thoughtful place for your tasks, deadlines, and small wins. Get the important things out of your head and into a plan that feels possible.</p>
            <div class="hero-actions">
                <?php if (current_user()): ?>
                    <a class="btn btn-lime btn-lg px-4" href="dashboard.php">Open your dashboard <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i></a>
                <?php else: ?>
                    <a class="btn btn-lime btn-lg px-4" href="auth/register.php">Start your list <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i></a>
                    <a class="btn btn-outline-light btn-lg px-4" href="auth/login.php">Log in</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section class="section-pad" id="how-it-works" aria-labelledby="steps-title">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Less juggling, more doing</span>
            <h2 id="steps-title">Your next step, made visible.</h2>
            <p>Daymark keeps your work lightweight: capture what needs doing, give it a place, then move through it at your own pace.</p>
        </div>
        <div class="steps-row">
            <article class="step-item"><span class="step-number">01 / CAPTURE</span><h3>Get it out of your head</h3><p>Add the task, a useful note, and a due date when it needs one.</p></article>
            <article class="step-item"><span class="step-number">02 / SORT</span><h3>See the shape of your day</h3><p>Filter by category or status, and keep the next action in view.</p></article>
            <article class="step-item"><span class="step-number">03 / FINISH</span><h3>Notice your progress</h3><p>Check off the work you complete and let the progress bar add it up.</p></article>
        </div>
    </div>
</section>
<section class="cta-band" aria-label="Get started">
    <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div><h2>Make a little space for your next win.</h2><p>Your task list is ready when you are.</p></div>
        <a class="btn btn-lime px-4" href="<?= current_user() ? 'dashboard.php' : 'auth/register.php' ?>"><?= current_user() ? 'Go to dashboard' : 'Create your account' ?> <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>