# MARUHOM Library Management System

A web-based Library Management System built with **CodeIgniter 4** and **PHP**. This system helps a school or organization manage books, borrowings, reservations, fines, and users all in one place.

---

## Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [User Roles](#user-roles)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Default Accounts](#default-accounts)
- [Project Structure](#project-structure)
- [How It Works](#how-it-works)
- [License](#license)

---

## About the Project

MARUHOM Library Management System is a full-stack web application. It allows library staff to manage books and borrowings easily. Students and teachers can browse the book catalog, reserve books, and track their borrowings online.

The system uses role-based access. Each user type sees only the pages and actions they are allowed to use.

---

## Features

### Authentication
- User registration (default role: Student)
- Login with **CAPTCHA** verification for added security
- Secure password hashing using PHP `PASSWORD_DEFAULT`
- Session-based login and logout
- Redirect to the correct dashboard based on user role

### Book Management
- Add, edit, and delete books
- Book details include: title, author, ISBN, publisher, publication year, category, description, cover image, total copies, available copies, and shelf location
- Search books by title, author, ISBN, publisher, or category
- Available copies are updated automatically when books are borrowed or returned

### Book Categories
- Create and manage book categories
- Each book is linked to a category

### Borrowing Management
- Issue books to students or teachers
- Record borrow date, due date, and return date
- Track borrowing status: `borrowed`, `overdue`, or `returned`
- View full borrowing history per user

### Book Reservation
- Students and teachers can reserve books online
- Reservations expire automatically after **48 hours**
- Users can cancel their own reservations
- Admin and librarian can view all pending reservations

### Fine Management
- Fines are calculated automatically for overdue books
- Rate: **PHP 10.00 per day** overdue
- Track fine status: `unpaid` or `paid`
- View total unpaid fines per user

### Notifications
- Users receive notifications for important events such as reservations and overdue notices
- Mark notifications as read
- Notification badge shown in the navigation bar

### Dashboards
- Each role has its own dashboard with relevant information
- Admin and Librarian see a summary of books, borrowings, fines, and reservations

### Public Pages
- Home page with library information
- About page
- Contact page

---

## User Roles

| Role          | What They Can Do |
|---------------|-----------------|
| **Admin**     | Manage users, books, categories, borrowings, and fines |
| **Librarian** | Manage books, categories, borrowings, and fines |
| **Teacher**   | Browse the book catalog, reserve books, view own borrowings |
| **Student**   | Browse the book catalog, reserve books, view own borrowings |

---

## Tech Stack

| Layer      | Technology |
|------------|-----------|
| Framework  | CodeIgniter 4 |
| Language   | PHP 8.1+ |
| Database   | MySQL |
| Frontend   | Bootstrap 5, Bootstrap Icons |
| Server     | Apache (XAMPP recommended) |
| CAPTCHA    | Custom PHP CAPTCHA |

---

## Requirements

Before you install, make sure you have the following:

- **PHP 8.1 or higher**
- **MySQL 5.7 or higher**
- **Apache web server** (XAMPP is recommended for local setup)
- **Composer** – PHP dependency manager
- The following PHP extensions must be enabled:
  - `intl`
  - `mbstring`
  - `mysqlnd`
  - `json`
  - `libcurl`

---

## Installation

Follow these steps to set up the project on your local machine.

### Step 1 – Copy the Project

Place the project folder inside your web server's root directory.

For XAMPP on Windows:
```
C:\xampp\htdocs\MARUHOM-LIBRARY
```

### Step 2 – Install Dependencies

Open a terminal inside the project folder and run:

```bash
composer install
```

### Step 3 – Set Up the Environment File

Copy the sample environment file and rename it:

```bash
cp env .env
```

Open `.env` and update the following values:

```env
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = lms_maruhom
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

> Change the `password` value if your MySQL root user has a password set.

### Step 4 – Set Up the Database

See the [Database Setup](#database-setup) section below.

### Step 5 – Set the Base URL

In your `.env` file, set the correct base URL:

```env
app.baseURL = 'http://localhost/MARUHOM-LIBRARY/public/'
```

### Step 6 – Open the Application

Start Apache and MySQL in XAMPP, then open your browser and go to:

```
http://localhost/MARUHOM-LIBRARY/public/
```

---

## Database Setup

### Step 1 – Create the Database

Open **phpMyAdmin** or any MySQL client and create a new database:

```sql
CREATE DATABASE lms_maruhom;
```

### Step 2 – Run Migrations

In the terminal, run the CodeIgniter migrations to create all the tables:

```bash
php spark migrate
```

### Step 3 – Seed the Librarian Account

A seed script is included to create a default librarian account. Run it from the project root:

```bash
php _seed_librarian.php
```

This will create the following account:

| Field    | Value             |
|----------|-------------------|
| Name     | fatima librarian  |
| Email    | fatima@gmail.com  |
| Password | fatima            |
| Role     | librarian         |

> **Important:** Change this password after your first login.

---

## Default Accounts

After setup, you can log in with the following default account:

| Role       | Email            | Password |
|------------|------------------|----------|
| Librarian  | fatima@gmail.com | fatima   |

> There is no default Admin account. You can create one by inserting a user directly into the database with `role = 'admin'`.

To register a new **Student** account, visit `/register` on the website.

---

## Project Structure

```
MARUHOM-LIBRARY/
├── app/
│   ├── Config/
│   │   ├── Routes.php              # All application routes
│   │   └── Database.php            # Database configuration
│   ├── Controllers/
│   │   ├── Auth.php                # Authentication and main logic
│   │   ├── Home.php                # Public pages (home, about, contact)
│   │   ├── Notifications.php       # Notification handling
│   │   └── CaptchaController.php   # CAPTCHA image generation
│   ├── Models/
│   │   ├── BookModel.php           # Book queries
│   │   ├── BookCategoryModel.php   # Category queries
│   │   ├── BorrowingModel.php      # Borrowing queries
│   │   ├── ReservationModel.php    # Reservation queries
│   │   ├── FineModel.php           # Fine calculation and queries
│   │   └── NotificationsModel.php  # Notification queries
│   └── Views/
│       ├── admin/                  # Admin panel views
│       ├── librarian/              # Librarian panel views
│       ├── student/                # Student views
│       ├── teacher/                # Teacher views
│       ├── auth/                   # Login and registration forms
│       └── templates/              # Shared layout templates
├── public/                         # Web root (point your server here)
├── writable/
│   ├── logs/                       # Application logs
│   ├── cache/                      # Cache storage
│   └── uploads/                    # Uploaded files (e.g., book covers)
├── _seed_librarian.php             # Script to create the default librarian account
├── composer.json                   # PHP dependencies
└── .env                            # Environment configuration (not committed to Git)
```

---

## How It Works

### For Students and Teachers
1. Go to the website and click **Register** to create an account.
2. Log in with your email and password. You must also complete the CAPTCHA.
3. Go to the **Catalog** to browse all available books.
4. Click **Reserve** on a book to place a reservation. The reservation is valid for 48 hours.
5. Go to **My Borrowings** to see the books you have borrowed and their due dates.
6. If you return a book late, a fine of **PHP 10.00 per day** will be added to your account.

### For Librarians and Admins
1. Log in with your librarian or admin account.
2. Use **Manage Books** to add, edit, or remove books.
3. Use **Manage Borrowings** to issue books to users and to record book returns.
4. Use **Manage Fines** to view overdue fines and mark them as paid.
5. Admins can also use **Manage Users** to view and manage all registered users.

---

## License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.
