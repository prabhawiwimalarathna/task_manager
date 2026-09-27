# Daymark Personal Task Manager

A responsive task manager mini project developed for the university web technologies course. The application features a frontend built with HTML, custom CSS, Bootstrap 5, and JavaScript, backed by a backend system driven by PHP and a MySQL database to handle persistent user profiles, secure contact inquiries, and task management.

## Features
- **Responsive Layout:** Adaptive home, protected dashboard, contact, login, and registration pages designed for mobile, tablet, and desktop environments.
- **User Authentication:** Secure account registration featuring server-side validation and secure cryptographic storage using `password_hash()`. User session login handles verification via `password_verify()`.
- **Dynamic Task Control:** Authenticated, per-user task tracking featuring creation, completion toggles, deletions, and real-time category filtering.
- **Live UI Rendering:** Fetch-based task state adjustments handling structured JSON streams for immediate client-side UI animations without page reloads.
- **Asynchronous Progress Tracking:** Interactive visual progress indicator tracking dynamic task completion statistics.
- **Inquiry Handling:** Validated contact data capture parsing clean server-side variables securely into a structural log schema.
- **Core Security Controls:** Form processing guarded with parameterized PDO statements, output escaping, and session handling variables.

## Technical Requirements
- Local stack server distribution (XAMPP or WAMP running Apache with PHP 8.1+ and MySQL/MariaDB).
- An active internet connection to parse structural design CDNs (Bootstrap 5 Core, Bootstrap Icons, and Google Fonts).

## XAMPP Setup Instructions
1. Copy the entire project repository folder into your XAMPP installation directory under:
   `C:\xampp\htdocs\task_manager`
2. Launch the **XAMPP Control Panel** and activate both the **Apache** and **MySQL** modules.
3. Open your preferred web browser and navigate to the database utility portal: `http://localhost/phpmyadmin/`
4. Create or select your workspace, click the **Import** tab on the navigation bar, choose the `database.sql` file located inside the root project directory, and click **Import**.
5. Verify that your core database configuration file at `project/includes/db.php` references your correct local host variables.
6. Launch the operational project dashboard interface by visiting:
   `http://localhost/task_manager/project/`

## WAMP Setup Instructions
1. Copy the project repository folder into your WAMP application storage directory under:
   `C:\wamp64\www\task_manager`
2. Start the WAMP server applications and ensure the status icon indicates all service architectures are running.
3. Access phpMyAdmin from the local WAMP landing screen, navigate to **Import**, supply the root directory's `database.sql` archive, and complete the schema installation.
4. Launch the operational project dashboard interface by visiting:
   `http://localhost/task_manager/project/`

## Default Database Settings
By default, the internal database engine connection routing via `project/includes/db.php` utilizes the following local variables:
- **Host address:** `127.0.0.1`
- **Database Name:** `task_manager`
- **Server Username:** `root`
- **Server Password:** *(blank / none)*

## Preloaded Demo Profile
The integrated SQL database setup script includes an existing profile designed for structural testing:
- **Username / Email:** `demo@example.com`
- **Account Password:** `password123`
*Alternatively, you can generate an independent profile directly using the built-in Registration interface.*

## Project Directory Tree
```text
task_manager/
└── project/
    ├── auth/                 # Account registration, login access, and session logout scripts
    ├── css/                  # Structured style sheets (custom responsive layouts and global systems)
    ├── images/               # Application image asset directory
    ├── includes/
    │   ├── db.php            # Active PDO object constructor and database connection layer
    │   ├── footer.php        # Global structural markup footer template and JavaScript imports
    │   ├── functions.php     # Session guards, input sanitization routines, and helper scripts
    │   ├── header.php        # Global structural navigation bar and metadata definitions
    │   └── task_actions.php  # Authenticated backend endpoint parsing responsive JSON task states
    ├── js/                   # Front-end behavioral control engine handling event validation and Fetch APIs
    ├── contact.php           # Public contact sub-module mapping customer query forms
    ├── dashboard.php         # Authenticated main interface for active tracking and metric analytics
    ├── index.php             # Core public landing index page
    └── database.sql          # Structured relational database system schema backup
```

## Security & Operational Architecture
- Data access isolation rules bind specific `tasks.user_id` values to individual authenticated accounts; reads, writes, and state mutations are securely scoped by session data.
- Task status parameters capture real-time change states via `completed_at` timestamps to compute metrics for the interface progress meters.
- This codebase is intended for internal academic assessment purposes. Do not deploy using default local credentials or open error streams in public production environments.
