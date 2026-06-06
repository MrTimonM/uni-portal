# University Portal

A PHP and MySQL university portal for Admin, Teacher, and Student workflows. The project runs locally with XAMPP and includes role-specific login screens, dashboards, academic records, attendance, results, payments, notices, events, support tickets, library catalog, local assets, and caching.

## Local Setup

1. Start Apache and MySQL from XAMPP.
2. Open the project in the browser:

```text
http://127.0.0.1:8080/index.php
```

If you are using Apache directly from XAMPP, place the project in `htdocs` and open the matching local URL.

The app auto-creates the `university_portal` database and required tables when it runs. The default DB user is `root` with an empty password. It tries MySQL ports `3312` and `3306`.

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
