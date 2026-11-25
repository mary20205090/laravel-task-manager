# Task Manager

A Laravel 13 application for creating, editing, deleting, and
reordering tasks, with MySQL storage and project filtering.

[GitHub repository](https://github.com/mary20205090/laravel-task-manager)

## Requirements

- PHP 8.3+ with Laravel's required extensions and `pdo_mysql`
- Composer 2
- MySQL 8.0+
- Node.js 22.12+ and npm for building frontend assets
- Git, if cloning the repository

PHP runs the application. Node.js and npm are used by Vite to build
the Tailwind CSS and browser JavaScript into `public/build`.

Node.js is not required on the production server if the assets are
built elsewhere and `public/build` is uploaded with the application.

## Setup

These instructions are for a fresh installation.

### 1. Download the project

Clone the repository:

```bash
git clone https://github.com/mary20205090/laravel-task-manager.git
cd laravel-task-manager
```

Alternatively, extract the ZIP and open a terminal in the project
folder containing `artisan` and `composer.json`. Continue from Step 2.

Run all remaining commands from the project directory.

### 2. Install dependencies and initialize the environment

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Create a MySQL database and user

Sign in to MySQL with an administrator account. Replace
`YOUR_PASSWORD` with a password of your choice.

```sql
CREATE DATABASE task_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER 'task_app'@'127.0.0.1'
    IDENTIFIED BY 'YOUR_PASSWORD';

GRANT ALL PRIVILEGES ON task_management.*
    TO 'task_app'@'127.0.0.1';

EXIT;
```

### 4. Configure `.env`

Update these settings using the same database credentials:

```dotenv
APP_NAME="Task Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=task_app
DB_PASSWORD='YOUR_PASSWORD'
```

### 5. Create the tables and build frontend assets

```bash
php artisan config:clear
php artisan migrate
npm ci
npm run build
```

### 6. Start the application

```bash
php artisan serve
```

Open http://127.0.0.1:8000.

Create a project, add tasks, and drag them into priority order.
Selecting a project filters its tasks automatically.
No database seeding is required.

## Deployment

Use a server with the PHP and MySQL requirements above, plus
Nginx or Apache configured to run Laravel through PHP-FPM.

1. Upload or clone the project onto the server.

2. Install production PHP dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. Build frontend assets:

   ```bash
   npm ci
   npm run build
   ```

   Alternatively, build them locally and upload `public/build`.
   In that case, omit the npm commands on the server.

4. For a fresh deployment, create a database and initialize `.env`
   and its application key as described in Setup. Use production
   database credentials and set:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   SESSION_SECURE_COOKIE=true
   ```

5. Apply migrations and cache the configuration:

   ```bash
   php artisan config:clear
   php artisan migrate --force
   php artisan optimize
   ```

6. Set the web server's document root to the project's `public`
   directory and configure Laravel request routing.

7. Give the web server write access to `storage` and
   `bootstrap/cache`, and enable HTTPS.

Preserve the existing `.env` and `APP_KEY` on subsequent deployments.
Use the web server for production traffic; `php artisan serve`
is for local development.

Web server configuration examples:
[Laravel deployment documentation](https://laravel.com/docs/13.x/deployment).
