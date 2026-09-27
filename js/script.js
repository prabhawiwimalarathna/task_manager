/* Browser-side validation and progressive task-manager interactions. */
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date();
    const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 10);

    document.querySelectorAll('form[data-validate]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const email = form.querySelector('input[type="email"]');
            const password = form.querySelector('input[type="password"]');
            const dueDate = form.querySelector('input[name="due_date"]');
            if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                email.setCustomValidity('Enter a valid email address.');
            } else if (password && password.value && password.value.length < 8) {
                password.setCustomValidity('Use at least 8 characters.');
            } else if (dueDate && dueDate.value && dueDate.value < localToday) {
                dueDate.setCustomValidity('Choose today or a future date.');
            }
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                form.reportValidity();
            }
        });
        form.addEventListener('input', (event) => event.target.setCustomValidity(''));
    });

    document.querySelectorAll('input[name="due_date"]').forEach((input) => { input.min = localToday; });

    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    const app = document.querySelector('[data-task-app]');
    if (!app) return;

    const list = app.querySelector('[data-task-list]');
    const empty = app.querySelector('[data-empty-state]');
    const form = app.querySelector('[data-task-form]');
    const statusButtons = [...app.querySelectorAll('[data-status-filter]')];
    const categorySelect = app.querySelector('[data-category-filter]');
    const countLabel = app.querySelector('[data-task-count]');
    const progressBar = app.querySelector('[data-progress-bar]');
    const progressValue = app.querySelector('[data-progress-value]');
    const progressLabel = app.querySelector('[data-progress-label]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let tasks = Array.isArray(window.initialTasks) ? window.initialTasks : [];
    let statusFilter = 'all';

    const escapeHtml = (value = '') => String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);

    const visibleTasks = () => tasks.filter((task) => {
        const matchesStatus = statusFilter === 'all' || task.status === statusFilter;
        const matchesCategory = !categorySelect.value || task.category === categorySelect.value;
        return matchesStatus && matchesCategory;
    });

    const render = (newTaskId = null) => {
        const selectedCategory = categorySelect.value;
        const categories = [...new Set(tasks.map((task) => task.category))].sort((first, second) => first.localeCompare(second));
        categorySelect.innerHTML = '<option value="">Every category</option>' + categories.map((category) => `<option value="${escapeHtml(category)}">${escapeHtml(category)}</option>`).join('');
        categorySelect.value = categories.includes(selectedCategory) ? selectedCategory : '';
        const visible = visibleTasks();
        list.innerHTML = visible.map((task) => `
            <li class="task-row${task.status === 'completed' ? ' is-completed' : ''}${String(task.id) === String(newTaskId) ? ' task-enter' : ''}" data-task-id="${escapeHtml(task.id)}">
                <button class="task-check" type="button" data-action="toggle" aria-label="${task.status === 'completed' ? 'Mark active' : 'Mark completed'}: ${escapeHtml(task.title)}" aria-pressed="${task.status === 'completed'}">
                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                </button>
                <div class="task-main">
                    <span class="task-title">${escapeHtml(task.title)}</span>
                    ${task.description ? `<p class="task-description">${escapeHtml(task.description)}</p>` : ''}
                    <div class="task-meta"><span class="task-category">${escapeHtml(task.category)}</span>${task.due_date ? `<span>Due ${escapeHtml(task.due_date)}</span>` : ''}</div>
                </div>
                <div class="task-actions">
                    <button class="icon-button" type="button" data-action="delete" aria-label="Delete ${escapeHtml(task.title)}" title="Delete task"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                </div>
            </li>`).join('');
        empty.hidden = visible.length > 0;
        countLabel.textContent = `${visible.length} ${visible.length === 1 ? 'task' : 'tasks'}`;
        updateProgress();
    };

    const updateProgress = () => {
        const completedToday = tasks.filter((task) => task.status === 'completed' && String(task.completed_at ?? '').slice(0, 10) === localToday).length;
        const percent = tasks.length ? Math.round((completedToday / tasks.length) * 100) : 0;
        progressBar.style.width = `${percent}%`;
        progressBar.setAttribute('aria-valuenow', String(percent));
        progressValue.textContent = `${percent}%`;
        progressLabel.textContent = `${completedToday} ${completedToday === 1 ? 'task' : 'tasks'} completed today`;
    };

    const request = async (payload) => {
        const response = await fetch('includes/task_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || 'Something went wrong. Please try again.');
        return result;
    };

    statusButtons.forEach((button) => button.addEventListener('click', () => {
        statusFilter = button.dataset.statusFilter;
        statusButtons.forEach((item) => item.classList.toggle('active', item === button));
        statusButtons.forEach((item) => item.setAttribute('aria-pressed', String(item === button)));
        render();
    }));
    categorySelect.addEventListener('change', () => render());

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const submit = form.querySelector('[type="submit"]');
        const formData = new FormData(form);
        submit.disabled = true;
        try {
            const result = await request({ action: 'add', title: formData.get('title'), description: formData.get('description'), category: formData.get('category'), due_date: formData.get('due_date') });
            tasks.unshift(result.task);
            form.reset();
            const dateInput = form.querySelector('[name="due_date"]');
            dateInput.min = localToday;
            render(result.task.id);
        } catch (error) {
            window.alert(error.message);
        } finally {
            submit.disabled = false;
        }
    });

    list.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;
        const row = button.closest('[data-task-id]');
        const task = tasks.find((item) => String(item.id) === row.dataset.taskId);
        if (!task) return;
        if (button.dataset.action === 'delete' && !window.confirm(`Delete "${task.title}"? This cannot be undone.`)) return;
        button.disabled = true;
        try {
            if (button.dataset.action === 'toggle') {
                const status = task.status === 'completed' ? 'active' : 'completed';
                const result = await request({ action: 'toggle', id: task.id, status });
                tasks = tasks.map((item) => String(item.id) === String(result.task.id) ? result.task : item);
                render();
            } else {
                await request({ action: 'delete', id: task.id });
                row.classList.add('task-exit');
                window.setTimeout(() => {
                    tasks = tasks.filter((item) => String(item.id) !== String(task.id));
                    render();
                }, 220);
            }
        } catch (error) {
            button.disabled = false;
            window.alert(error.message);
        }
    });

    render();
});