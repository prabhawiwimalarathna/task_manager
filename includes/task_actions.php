<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

if (!current_user()) {
    json_response(['success' => false, 'message' => 'Your session ended. Please log in again.'], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Only POST requests are accepted.'], 405);
}
if (!verify_csrf_token($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    json_response(['success' => false, 'message' => 'Your form session expired. Refresh the page and try again.'], 403);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    json_response(['success' => false, 'message' => 'The request was not valid JSON.'], 400);
}
$userId = (int) current_user()['id'];

try {
    $pdo = get_db();
    if (($input['action'] ?? '') === 'add') {
        $title = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $category = trim((string) ($input['category'] ?? '')) ?: 'General';
        $dueDate = trim((string) ($input['due_date'] ?? ''));
        if ($title === '' || strlen($title) > 150) {
            json_response(['success' => false, 'message' => 'Task title is required and must be at most 150 characters.'], 422);
        }
        if (strlen($description) > 2000 || strlen($category) > 50) {
            json_response(['success' => false, 'message' => 'The note or category is too long.'], 422);
        }
        if (!valid_optional_date($dueDate)) {
            json_response(['success' => false, 'message' => 'Due date must be valid and cannot be in the past.'], 422);
        }
        $statement = $pdo->prepare('INSERT INTO tasks (user_id, title, description, category, due_date) VALUES (:user_id, :title, :description, :category, :due_date)');
        $statement->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description !== '' ? $description : null,
            'category' => $category,
            'due_date' => $dueDate !== '' ? $dueDate : null,
        ]);
        $taskId = (int) $pdo->lastInsertId();
        $statement = $pdo->prepare('SELECT id, title, description, category, due_date, status, completed_at FROM tasks WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $taskId, 'user_id' => $userId]);
        json_response(['success' => true, 'task' => $statement->fetch()], 201);
    }

    if (($input['action'] ?? '') === 'toggle') {
        $taskId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $status = $input['status'] ?? '';
        if (!$taskId || !in_array($status, ['active', 'completed'], true)) {
            json_response(['success' => false, 'message' => 'Task update was not valid.'], 422);
        }
        $statement = $pdo->prepare('UPDATE tasks SET status = :status, completed_at = CASE WHEN :completed_status = \'completed\' THEN NOW() ELSE NULL END WHERE id = :id AND user_id = :user_id');
        $statement->execute(['status' => $status, 'completed_status' => $status, 'id' => $taskId, 'user_id' => $userId]);
        if ($statement->rowCount() === 0) {
            $exists = $pdo->prepare('SELECT id FROM tasks WHERE id = :id AND user_id = :user_id');
            $exists->execute(['id' => $taskId, 'user_id' => $userId]);
            if (!$exists->fetch()) {
                json_response(['success' => false, 'message' => 'Task not found.'], 404);
            }
        }
        $statement = $pdo->prepare('SELECT id, title, description, category, due_date, status, completed_at FROM tasks WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $taskId, 'user_id' => $userId]);
        json_response(['success' => true, 'task' => $statement->fetch()]);
    }

    if (($input['action'] ?? '') === 'delete') {
        $taskId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$taskId) {
            json_response(['success' => false, 'message' => 'Task ID was not valid.'], 422);
        }
        $statement = $pdo->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $taskId, 'user_id' => $userId]);
        if ($statement->rowCount() !== 1) {
            json_response(['success' => false, 'message' => 'Task not found.'], 404);
        }
        json_response(['success' => true]);
    }

    json_response(['success' => false, 'message' => 'Unknown task action.'], 422);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    json_response(['success' => false, 'message' => 'The task could not be saved. Check the database setup and try again.'], 500);
}