<?php
$pageTitle = $pageTitle ?? 'Personal Task Manager';
$currentPage = $currentPage ?? '';
$loggedInUser = current_user();
$basePath = str_contains(strtolower($_SERVER['SCRIPT_NAME'] ?? ''), '/auth/') ? '../' : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle) ?> | Daymark</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e($basePath) ?>css/style.css" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable" href="#main-content">Skip to content</a>
<nav class="navbar navbar-expand-lg site-nav" aria-label="Main navigation">
    <div class="container">
        <a class="brand-mark" href="<?= e($basePath) ?>index.php" aria-label="Daymark home">daymark<span>.</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#site-navigation" aria-controls="site-navigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="site-navigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link<?= $currentPage === 'home' ? ' active' : '' ?>" href="<?= e($basePath) ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link<?= $currentPage === 'dashboard' ? ' active' : '' ?>" href="<?= e($basePath) ?>dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link<?= $currentPage === 'contact' ? ' active' : '' ?>" href="<?= e($basePath) ?>contact.php">Contact</a></li>
                <?php if ($loggedInUser): ?>
                    <li class="nav-item"><span class="nav-link small">Hi, <?= e($loggedInUser['username']) ?></span></li>
                    <li class="nav-item">
                        <form action="<?= e($basePath) ?>auth/logout.php" method="post" class="m-0">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Log out</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e($basePath) ?>auth/login.php">Log in</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-sm nav-cta px-3" href="<?= e($basePath) ?>auth/register.php">Create account</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main id="main-content">