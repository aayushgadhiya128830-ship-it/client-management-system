# Client Management System

BCA project 2026–27, Marwadi University — **Aayush Gadhiya (92400527237)**, guide **Amit Gohel**.

A Laravel application that keeps client records in a central MySQL database.
Users can add, list, view, edit and delete clients, search by name / email / phone
and browse the list with pagination (10 per page). The interface uses a
neumorphic (soft UI) design built with plain CSS – no external CSS/JS library or CDN is needed.

## Objectives
- Replace paper registers and spreadsheets with one validated digital system.
- Practise Laravel MVC, Composer, Eloquent, migrations, Form Requests and Blade.
- Provide search, pagination, clear messages and a delete confirmation.

## Technology
PHP 8.3+ (PHP 8.2 works with Laravel 12) · Laravel 13 or 12 · Composer 2 · MySQL 8 / MariaDB · Blade · HTML5 · CSS3 · small vanilla JavaScript · Git/GitHub

## Installation (Windows, XAMPP/Laragon)
This package contains only the application files. Add them to a fresh Laravel project:

```bat
composer create-project laravel/laravel client-management
rem PHP 8.2 only:  composer create-project laravel/laravel:^12.0 client-management
cd client-management
```
Copy the folders `app`, `database`, `docs`, `public`, `resources`, `routes`, `tests`
and this `README.md` from the package into the project folder and choose **Replace**.
(They replace `routes/web.php`, `AppServiceProvider.php` and `DatabaseSeeder.php`.)

```bat
copy .env.example .env
php artisan key:generate
```
Create an empty MySQL database named `client_management` (phpMyAdmin) and edit `.env`:
```
APP_NAME="Client Management System"
APP_ENV=local
APP_DEBUG=true            # use false on a real server
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=client_management
DB_USERNAME=root
DB_PASSWORD=
```
Then:
```bat
php artisan migrate --seed     rem tables + 8 sample clients (safe: does not erase data)
php artisan serve              rem open http://127.0.0.1:8000
php artisan test               rem automated tests
```
> Never run `php artisan migrate:fresh` or `db:wipe` on a database that holds real clients – they delete all data.
> `.env` is already ignored by Git; never commit it.

### Running the tests
Tests use SQLite in memory. In `phpunit.xml` these two lines must be active (not commented),
and `extension=pdo_sqlite` must be enabled in `php.ini`:
```
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Features
Client list · Add · View details · Edit · Delete (confirmation dialog, DELETE method + CSRF) ·
Search (name, email, phone; partial; wildcard-safe) · Pagination (10, search kept) ·
Server-side validation (unique email, phone 10–15 digits, status active/inactive) ·
Success / error messages · Responsive layout (table turns into cards on phones) · Keyboard-accessible forms and dialog ·
Smooth animations (page, rows, dialog, messages; switched off automatically for users who prefer reduced motion) ·
Live search and pagination without page reload.

## Project structure (files added to Laravel)
```
app/Models/Client.php                      model, search scope, initials
app/Http/Controllers/ClientController.php  index, create, store, show, edit, update, destroy
app/Http/Requests/ClientRequest.php        validation for create + update
app/Providers/AppServiceProvider.php       neumorphic pagination view
routes/web.php                             "/" -> /clients, Route::resource('clients')
database/migrations/                       clients table + indexes (data-safe)
database/factories/ClientFactory.php       test / demo data
database/seeders/                          8 sample clients
resources/views/layouts/app.blade.php      shared layout
resources/views/clients/                   index, create, edit, show, _form, _delete_modal
resources/views/components/icon.blade.php  inline SVG icons
resources/views/vendor/pagination/         neumorphic pagination
public/css/neumorphic.css, public/js/app.js   styles + animations, live search, dialog
tests/Feature/ClientTest.php               automated tests
docs/AUDIT_AND_TRACEABILITY.md             audit findings + requirement checklist
```

## Animations
- Animations are CSS only (`public/css/neumorphic.css`). They are disabled when the device setting "reduce motion" is on.
- Live search waits 350 ms after you stop typing; if JavaScript fails it falls back to a normal page load.

## Common problems
| Problem | Solution |
|---|---|
| `could not find driver` | enable `extension=pdo_mysql` (and `pdo_sqlite` for tests) in `php.ini`, restart Apache |
| `Access denied for user` / `Unknown database` | check `DB_*` values in `.env` and create the database first |
| `No application encryption key` | run `php artisan key:generate` |
| Page shows no styling | make sure `public/css/neumorphic.css` was copied; run `php artisan serve` from the project root |
| Composer says PHP version too low | use `laravel/laravel:^12.0` (PHP 8.2) or upgrade to PHP 8.3 |
| Tests fail with "no such table" / driver | see "Running the tests" above |
