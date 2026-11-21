# Client Management System

A Laravel web application that keeps client records in a central MySQL database.
Users can add, list, view, edit and delete clients, search by name / email / phone and
browse the list with pagination (10 per page). The interface is built in the
neumorphic (soft UI) style with plain CSS and a small amount of JavaScript – no CSS framework and no CDN.

| | |
|---|---|
| **Student** | Aayush Gadhiya (Enrollment No. 92400527237) |
| **Internal guide** | Amit Gohel |
| **Programme** | Bachelor of Computer Applications (BCA), 2026–27 |
| **University** | Marwadi University, Faculty of Computer Applications |

## Features
- Client list, add, view details, edit and delete (delete asks for confirmation; DELETE method with CSRF token)
- Server-side validation: required name, valid and unique email, phone with 10–15 digits, status active / inactive
- Search by name, email or phone (partial, case-insensitive, safe against wildcard and SQL input)
- Pagination, exactly 10 records per page, search term kept on every page
- Live search and page change without reloading (falls back to normal links if JavaScript is off)
- Success and error messages, empty-state message, responsive layout (table becomes cards on phones)
- Short animations (switched off automatically for users who prefer reduced motion), keyboard-friendly forms and dialog
- 27 automated feature tests

## Technology
PHP 8.3+ · Laravel 13 · Composer 2 · MySQL / MariaDB · Blade · HTML5 · CSS3 · vanilla JavaScript · Git

## Installation
Requirements: PHP 8.3 or newer with `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip` (and `pdo_sqlite` for the tests), Composer 2, MySQL or MariaDB.

```bash
git clone <repository-url> client-management
cd client-management
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```
Create an empty database named `client_management`, then edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=client_management
DB_USERNAME=your_mysql_user
DB_PASSWORD=your_mysql_password
```
Then create the tables with sample data and start the server:
```bash
php artisan migrate --seed
php artisan serve
```
Open http://127.0.0.1:8000 (the home page redirects to `/clients`).

> Never run `php artisan migrate:fresh` or `db:wipe` on a database that holds real clients – they delete all data.
> `.env` is ignored by Git; never commit real passwords.

## Run the tests
```bash
php artisan test
```
The tests use SQLite in memory (`phpunit.xml`), so your MySQL data is not touched. Enable `pdo_sqlite` in `php.ini` if needed.

## Project structure (Client module)
```
app/Models/Client.php                       model, search scope, initials
app/Http/Controllers/ClientController.php   index, create, store, show, edit, update, destroy
app/Http/Requests/ClientRequest.php         validation for create and update
app/Providers/AppServiceProvider.php        neumorphic pagination view
routes/web.php                              "/" -> /clients, Route::resource('clients')
database/migrations/                        clients table and indexes
database/seeders/, database/factories/      sample and test data
resources/views/layouts/app.blade.php       shared layout
resources/views/clients/                    index, create, edit, show, _form, _delete_modal
resources/views/components/icon.blade.php   inline SVG icons
public/css/neumorphic.css, public/js/app.js styles + animations, live search, dialog
tests/Feature/ClientTest.php                automated tests
docs/AUDIT_AND_TRACEABILITY.md              audit notes and requirement checklist
```

## Database: `clients` table
| Column | Type | Rule |
|---|---|---|
| id | BIGINT UNSIGNED | primary key |
| name | VARCHAR(100) | required |
| email | VARCHAR(150) | required, unique |
| phone | VARCHAR(15) | required, 10–15 digits |
| company_name | VARCHAR(150) | optional |
| address | TEXT | optional |
| city | VARCHAR(80) | optional |
| status | ENUM('active','inactive') | default active |
| created_at, updated_at | TIMESTAMP | automatic |

## Common problems
| Problem | Solution |
|---|---|
| `could not find driver` | enable `pdo_mysql` (and `pdo_sqlite` for tests) in `php.ini` |
| `Access denied` / `Unknown database` | check the `DB_*` values in `.env` and create the database first |
| `Connection refused` on port 3306 | start MySQL / MariaDB (XAMPP, Laragon, or `sudo service mariadb start`) |
| `No application encryption key` | run `php artisan key:generate` |
| Composer says PHP version too low | upgrade to PHP 8.3 or newer |

## Out of scope
Login and roles, billing, e-mail / SMS, reports and public API. The project stays a focused client record manager.
