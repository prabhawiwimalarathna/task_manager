<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
require_login();

$statement = get_db()->prepare('SELECT id, title, description, category, due_date, status, completed_at FROM tasks WHERE user_id = :user_id ORDER BY (status = \'completed\'), due_date IS NULL, due_date ASC, created_at DESC');
$statement->execute(['user_id' => current_user()['id']]);
$tasks = $statement->fetchAll();
$categories = array_values(array_unique(array_map(static fn (array $task): string => $task['category'], $tasks)));

$pageTitle = 'Your dashboard';
$currentPage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="container dashboard-shell" data-task-app>
    <header class="page-intro">
        <span class="eyebrow">Your space, <?= e(current_user()['username']) ?></span>
        <h1>Make today count.</h1>
        <p>Keep the next small step close. Add a task, choose what needs attention, and mark your progress as you go.</p>
    </header>

    <div class="dashboard-toolbar">
        <div class="filter-group" role="group" aria-label="Filter tasks by status">
            <span class="filter-label">Show</span>
            <button class="filter-btn active" type="button" data-status-filter="all" aria-pressed="true">All</button>
            <button class="filter-btn" type="button" data-status-filter="active" aria-pressed="false">Active</button>
            <button class="filter-btn" type="button" data-status-filter="completed" aria-pressed="false">Completed</button>
        </div>
        <label class="d-flex align-items-center gap-2"><span class="filter-label mb-0">Category</span>
            <select class="form-select form-select-sm category-select" data-category-filter aria-label="Filter by category">
                <option value="">Every category</option>
                <?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?= e($category) ?></option><?php endforeach; ?>
            </select>
        </label>
    </div>

    <section class="progress-section" aria-label="Task completion progress">
        <div>
            <div class="progress-copy"><strong>Today's progress</strong><span data-progress-label>Loading progress</span></div>
            <div class="progress" role="progressbar" aria-label="Tasks completed today" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar" data-progress-bar style="width: 0%"></div></div>
        </div>
        <span class="progress-count" data-progress-value>0%</span>
    </section>

    <section class="task-form-wrap" aria-labelledby="add-task-title">
        <h2 id="add-task-title">Add a task</h2>
        <form class="task-form row g-3" data-task-form data-validate novalidate>
            <div class="col-12 col-lg-5"><label class="form-label" for="task-title">Task</label><input class="form-control" id="task-title" name="title" maxlength="150" placeholder="What needs doing?" required><div class="invalid-feedback">Give your task a title.</div></div>
            <div class="col-12 col-lg-3"><label class="form-label" for="task-category">Category</label><input class="form-control" id="task-category" name="category" list="task-categories" maxlength="50" placeholder="General"><datalist id="task-categories"><?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?php endforeach; ?><option value="General"><option value="Study"><option value="Work"><option value="Personal"><option value="Health"></datalist></div>
            <div class="col-12 col-md-6 col-lg-2"><label class="form-label" for="task-due-date">Due date</label><input class="form-control" id="task-due-date" name="due_date" type="date" min="<?= date('Y-m-d') ?>"></div>
            <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add task</button></div>
            <div class="col-12"><label class="form-label" for="task-description">Note <span class="text-secondary fw-normal">(optional)</span></label><input class="form-control" id="task-description" name="description" maxlength="2000" placeholder="Add a helpful detail"></div>
        </form>
    </section>

    <section aria-labelledby="task-list-title">
        <div class="task-list-heading"><h2 id="task-list-title">Your list</h2><span data-task-count>0 tasks</span></div>
        <ul class="task-list" data-task-list aria-live="polite"></ul>
        <div class="empty-state" data-empty-state hidden><strong>No tasks in this view.</strong>Try another filter or add something to your list.</div>
    </section>
</section>
<script>
    window.initialTasks = <?= json_encode($tasks, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>