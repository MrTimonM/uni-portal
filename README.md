# University Portal

A PHP and MySQL university portal for Admin, Teacher, and Student workflows. The project runs locally with XAMPP and includes role-specific login screens, dashboards, academic records, attendance, results, payments, notices, events, support tickets, library catalog, local assets, and caching.

## One-Click Start for XAMPP

For Windows friends using XAMPP, use:

```text
start_university_portal.bat
```

The launcher will:

- Start XAMPP MySQL if it is not already running.
- Import `university_portal.sql` into MySQL when the database is missing.
- Ask before re-importing if `university_portal` already exists.
- Start a local PHP server.
- Open the project in the browser.

Default project URL:

```text
http://127.0.0.1:8080/index.php
```

If port `8080` is busy, the launcher will try the next free port between `8081` and `8085`.

## Manual Setup

1. Start MySQL from XAMPP.
2. Import `university_portal.sql` in phpMyAdmin.
3. Start the PHP server from the project folder:

```text
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t .
```

4. Open:

```text
http://127.0.0.1:8080/index.php
```

The app uses `university_portal` as the primary database and does not create the database from PHP by default. The default DB user is `root` with an empty password. It connects to `127.0.0.1:3306` by default.

## Test Accounts

All test account passwords:

```text
12345
```

### Admin

```text
admin@gmail.com
```

### Teachers

```text
mahmudul.karim@uiu.com
nusrat.jahan@uiu.com
rezaul.haque@uiu.com
```

### Students

```text
tanvir.hossain@uiu.com
mehjabin.akter@uiu.com
samiul.islam@uiu.com
```

## Role Pages

- Admin login: `admin_login.php`
- Teacher login: `teacher_login.php`
- Student login: `student_login.php`
- Role chooser: `index.php`

## Features

- Role-specific dashboards
- Admin management for students, teachers, courses, and sections
- Teacher attendance and result submission
- Student advising, attendance, results, and payment views
- Notices and academic events
- Support desk
- Library catalog
- Local images and logo for fast loading
- Server-side query caching and browser asset caching
