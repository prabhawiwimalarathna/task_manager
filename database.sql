-- Personal Task Manager
-- Import this file in phpMyAdmin or run it with the MySQL client.

CREATE DATABASE IF NOT EXISTS task_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE task_manager;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'General',
    due_date DATE NULL,
    status ENUM('active', 'completed') NOT NULL DEFAULT 'active',
    completed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tasks_user_due (user_id, due_date),
    CONSTRAINT fk_tasks_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo account: demo@example.com / password123
INSERT INTO users (username, email, password)
VALUES ('demo_user', 'demo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeIcO/YJyeHf3XA0Y8B0Zpi1qgTIcqzgO')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO tasks (user_id, title, description, category, due_date, status, completed_at)
SELECT id, 'Finish web technologies project', 'Polish the task manager and verify the responsive layout.', 'Study', DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'active', NULL
FROM users WHERE email = 'demo@example.com'
AND NOT EXISTS (SELECT 1 FROM tasks WHERE title = 'Finish web technologies project' AND user_id = users.id);

INSERT INTO tasks (user_id, title, description, category, due_date, status, completed_at)
SELECT id, 'Read a chapter', 'Twenty focused minutes before dinner.', 'Personal', CURDATE(), 'completed', NOW()
FROM users WHERE email = 'demo@example.com'
AND NOT EXISTS (SELECT 1 FROM tasks WHERE title = 'Read a chapter' AND user_id = users.id);