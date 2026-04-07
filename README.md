<div align="center">

<img src="https://zh-innovation.com/logo.png" alt="ZH-Innovation Logo" width="120" style="border-radius: 16px;" />

# 🚀 ZH-Innovation API System

### *Laravel 12 · RESTful · Production-Ready*

> نظام متكامل لإدارة المحتوى، التوظيف، والمشاريع البرمجية — مبني بأحدث إصدار من Laravel 12

<br/>

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2F8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Sanctum](https://img.shields.io/badge/Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![License](https://img.shields.io/badge/License-MIT-22C55E?style=for-the-badge)](LICENSE)

<br/>

**Developed by AbdalrhmanAbdoAlhade**

[![Portfolio](https://img.shields.io/badge/Portfolio-000000?style=for-the-badge&logo=vercel&logoColor=white)](https://abdalrhman-abdo-alhade.vercel.app/)
[![Email](https://img.shields.io/badge/Gmail-EA4335?style=for-the-badge&logo=gmail&logoColor=white)](mailto:abdo.king22227@gmail.com)
[![GitHub](https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/abdalrhman-abdalnabe)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/201023402756)

![Profile Views](https://komarev.com/ghpvc/?username=AbdalrhmanAbdoAlhade&color=0e75b6&style=flat&label=Profile+Views)

</div>

---

## 📌 Table of Contents

- [Overview](#-overview)
- [Tech Stack](#️-tech-stack)
- [Key Features](#-key-features)
- [API Endpoints](#-api-endpoints)
- [Getting Started](#-getting-started)
- [Important Notes](#️-important-notes)
- [Contact](#-contact)

---

## 🧩 Overview

**ZH-Innovation API** is a production-grade backend system built on **Laravel 12**, designed to power full-featured digital platforms. It provides a robust set of RESTful JSON APIs covering job boards, CMS, project showcases, team management, and contact workflows — all secured with token-based authentication via Laravel Sanctum.

Whether you're integrating a mobile app or a web frontend, this system gives you clean, structured endpoints and admin-level control from day one.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12.x (Latest) |
| Authentication | Laravel Sanctum (Token-based) |
| Database | MySQL 8.x |
| Language | PHP 8.3 / 8.4 |
| Architecture | RESTful API · JSON Responses |

---

## ✨ Key Features

### 💼 Job Board System
- Admins can post jobs with type, location, and salary details
- Applicants can submit CVs (PDF/Word) via multipart upload
- Full application lifecycle management: `pending` → `accepted` / `rejected`

### 📝 Content Management (CMS)
- Blog posts with nested comment & reply support
- Manage Skills, Services, and Experience entries
- Team Members and Partners directory

### 💻 Projects & Portfolio
- Showcase software projects with live demo links

### 📧 Contact System
- Integrated contact form with email notification support

### 🛡️ Security & Permissions
- Route protection via custom Admin Middleware
- Token-secured access with Laravel Sanctum
- Full role separation: Guest / Authenticated / Admin

---

## 📂 API Endpoints

### 🔓 Public Routes

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/login` | User login |
| `GET` | `/api/jobs` | List all open jobs |
| `GET` | `/api/jobs/{id}` | Single job details |
| `POST` | `/api/jobs/{id}/apply` | Apply for a job |
| `GET` | `/api/blog` | Browse blog posts |

### 🔐 Authenticated Routes

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/logout` | Logout current session |
| `POST` | `/api/blog/{id}/comments` | Add a comment or reply |
| `PUT` / `DELETE` | `/api/comments/{id}` | Manage own comments |

### 🔴 Admin-Only Routes

| Method | Endpoint | Description |
|---|---|---|
| `POST` / `PUT` / `DELETE` | `/api/jobs` | Full job management |
| `GET` | `/api/jobs/{id}/applications` | View applicants |
| `POST` | `/api/applications/{id}/status` | Update applicant status |
| `CRUD` | `/api/blog-posts` | Manage blog posts |
| `CRUD` | `/api/skills` | Manage skills |
| `CRUD` | `/api/services` | Manage services |

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/username/zh-innovation-api.git
cd zh-innovation-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
cp .env.example .env
```

Then update your `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Initialize the Application

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 5. Start the Server

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`

---

## ⚠️ Important Notes

> **JSON Responses** — All endpoints return JSON. Always include the header:
> ```
> Accept: application/json
> ```

> **File Uploads** — When submitting a CV via `/api/jobs/{id}/apply`, use `multipart/form-data` encoding.

> **Laravel 12** — This project leverages Laravel 12's new route definitions and middleware configuration via `bootstrap/app.php`.

---

## 📬 Contact

<div align="center">

Built and maintained by **AbdalrhmanAbdoAlhade** — Senior Back-End Systems Engineer

| Channel | Link |
|---|---|
| 🌐 Website | [zh-innovation.com](https://zh-innovation.com) |
| 📧 Email | [admin@zh-innovation.com](mailto:admin@zh-innovation.com) |
| 💼 Portfolio | [abdalrhman-abdo-alhade.vercel.app](https://abdalrhman-abdo-alhade.vercel.app/) |
| 💬 WhatsApp | [+20 102 340 2756](https://wa.me/201023402756) |

<br/>

*Contributions are welcome — open a PR or reach out directly.*

<br/>

**© 2026 ZH-Innovation. All rights reserved.**

</div>
