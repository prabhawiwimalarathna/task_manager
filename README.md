# Daymark Personal Task Manager

A responsive task manager mini project for a university web technologies course. The frontend uses HTML, custom CSS, Bootstrap 5, and JavaScript; PHP and MySQL provide accounts, contact submissions, and persistent task management.

## Features

- Responsive home, protected dashboard, contact, login, and registration pages.
- Account registration with server-side validation and `password_hash()`, and login with `password_verify()`.
- Per-user task creation, completion toggles, deletion, status/category filters, and an animated completion-today progress bar.
- Fetch-based task actions with JSON responses and immediate UI updates.
- Contact form submissions saved in MySQL, with an inline success state.
- Client-side form validation, server-side input validation, prepared PDO statements, output escaping, session protections, and CSRF checks on state-changing requests.

## Requirements

- XAMPP or WAMP with Apache, PHP 8.1+ (PDO MySQL enabled), and MySQL/MariaDB.
- A modern browser and an internet connection for the Bootstrap, Bootstrap Icons, and Google Fonts CDN resources.

## XAMPP setup

1. Copy the `project` folder into XAMPP's `htdocs` directory. For example: `C:\xampp\htdocs\daymark`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open `http://localhost/phpmyadmin/` and choose **Import**.
4. Select `database.sql` from the copied project folder and choose **Import**. The script creates and selects the `task_manager` database, tables, and demo data.
5. Open `includes/db.php`. Its defaults target a local database at `127.0.0.1`, database `task_manager`, user `root`, and a blank password (the XAMPP default). Change those values if your local MySQL credentials differ.
6. Visit `http://localhost/daymark/`.

## WAMP setup

1. Copy the `project` folder into WAMP's `www` directory, for example `C:\wamp64\www\daymark`.
2. Start WAMP services and confirm the tray icon indicates Apache and MySQL are running.
3. Open phpMyAdmin from the WAMP start page, choose **Import**, select the project's `database.sql`, and import it.
4. Confirm the database values in `includes/db.php`. The defaults are database `task_manager`, user `root`, and a blank password; adjust them to match your WAMP installation.
5. Visit `http://localhost/daymark/`.

## Database configuration

By default, `includes/db.php` connects to:

```text
Host:     127.0.0.1
Port:     3307
Database: task_manager
Username: root
Password: (blank)
```

Update the values at the top of `includes/db.php`, or set the optional environment variables `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS`. The connection uses PDO, UTF-8 (`utf8mb4`), native prepared statements, and exception mode.

## Demo account

The imported database includes a demo account: `demo@example.com` with password `password123`. Register a new account to create a separate user and task list.

## Project structure

```text
project/
├── auth/                 # Registration, login, logout
├── css/style.css         # Responsive visual system and page styles
├── images/               # Local image assets (currently empty)
├── includes/
│   ├── db.php            # PDO connection
│   ├── footer.php        # Shared page footer and scripts
│   ├── functions.php     # Session, escaping, CSRF, and validation helpers
│   ├── header.php        # Shared navigation and document head
│   └── task_actions.php  # Authenticated JSON task endpoint
├── js/script.js          # Validation, filters, fetch, and UI animations
├── contact.php
├── dashboard.php
├── index.php
└── database.sql
```

## Notes

- `tasks.user_id` is tied to the authenticated account; reads, updates, and deletes are scoped by both task ID and user ID.
- `completed_at` records when a task is marked complete and drives the dashboard's completed-today indicator.
- Do not deploy with the sample database credentials. Use a dedicated MySQL user with only the privileges the app needs, configure HTTPS, and keep production errors out of browser responses.