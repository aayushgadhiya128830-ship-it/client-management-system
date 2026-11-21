# Audit and requirement traceability – Client Management System (Milestone 2)

## Run log – Run 1 (student's device: Kali NetHunter, PHP 8.4.27, Laravel 13.11.0, Composer 2.10.3, MariaDB)
- `composer create-project`: OK, "No security vulnerability advisories found".
- `php artisan migrate --seed`: all 5 migrations and ClientSeeder OK on MySQL.
- `php artisan route:list`: 7 client routes + home redirect present.
- `php artisan serve`: server started; pages, CSS and JS were requested and served (log). The process was later killed by Android (signal 9) – restart it.
- `php artisan test`: **25 passed, 1 failed** (`empty blank or array search is safe`: `?search[]=x` → HTTP 500).
- Cause: index view printed `request('search')` (an array). Fix: controller passes a sanitised `$search` string to the view. 

## Run log – Run 2 (same device, after the fix)
- `php artisan test`: **26 passed (100 assertions)**, 5.49 s. The defect from run 1 is fixed.
- `php artisan migrate:status` failed with "Connection refused" because MariaDB had stopped (Android killed the process). Not an application defect; start MariaDB with `sudo service mariadb start` and run it again.
- `composer validate` / `composer audit`: not yet run (Composer must not be run as root).

## Run log – Run 3 (clean reinstall on the same device)
Project, database and login were deleted and everything was installed again from the README steps (Laravel 13.11.0, PHP 8.4.27, Composer 2.10.3, MariaDB).
- `migrate --seed`: 5 migrations + ClientSeeder OK; `migrate:status`: all 5 **Ran**.
- `php artisan test`: **26 passed (100 assertions)**, 4.98 s.
- `composer validate`: valid. `composer audit`: **No security vulnerability advisories found.**
- Not yet done: `php artisan view:cache`, visual check of all pages in a browser (desktop/tablet/mobile), keyboard check.

## Run log – Run 4 (after adding animations, sounds and live search)
- `php artisan test`: 28 passed, 1 failed (108 assertions). The failure was in the new test `clear link is hidden...`: its pattern required `hidden>` but the page prints `hidden >`. Application behaviour was correct; the test pattern was fixed (`hidden\s*>`). Re-run needed: expect 29 passed.
- Tip: run artisan as the `kali` user (running as root creates root-owned files in `storage/`).

## Run log – Run 5 (clean reinstall with animations, sounds, live search)
- Project, database and login deleted and installed again from the README steps.
- `migrate --seed`: 5 migrations + ClientSeeder OK. `php artisan test`: **29 passed (109 assertions)**, 5.53 s. Server started on port 8001.
- Still to be checked by the student on the phone: animation smoothness, sound, live search, card layout (manual items R12, R13 and test case 14).

## Change 5 – sounds removed (requested by the student)
All sound code was removed (Web Audio code in `app.js`, the Sound switch in the menu, `data-sound` markers, sound icons, sound CSS, 2 sound-related test assertions). Animations, live search and the card layout remain. Expected test result after this change: 28 passed (27 ClientTest + 1 default Unit test) – **to be confirmed by running `php artisan test`**.

## Change 4 – animations, live search, flexible layout (sounds were removed later, see Change 5) (requested by the student)
Not part of the original scope list; added on request and kept small (no library, no sound files).
- Files: `public/js/app.js`, `public/css/neumorphic.css`, `layouts/app.blade.php`, `clients/index.blade.php`, `components/icon.blade.php`, 3 new tests (28 tests in ClientTest).
- Checked in a headless Chromium against a mock of the page HTML (same markup): live search (1 row for "isha", empty state, Clear button), pagination without reload, dialog open/close, sound on/off + remembered after reload, success sound on message, reduced-motion = no animation, phone layout (cards, no sideways scroll), no console errors.
- Defects found by that check and fixed: Clear button not hidden (author CSS overrode `hidden`), Actions column cut off on medium screens.
- **Not verified:** the Blade pages themselves with the new markup (run `php artisan test` – expect 29 tests incl. the default one), real device sound (needs your ears) and real device smoothness.

## A. Phase 1 – Discovery (what was actually available when the package was written)
| Item | Result |
|---|---|
| Project root | **Not available.** No Laravel project (`artisan`, `composer.json`, `.env.example`, `vendor`) exists in the workspace. |
| PHP / Laravel / Composer / MySQL | **Not installed** in the workspace (no PHP, no Composer, no MySQL, no network). |
| Baseline | Only the application files written for Milestone 2 (this package). They were audited and upgraded; they have **never been executed**. |
| Existing client data | None known. No migration was run, no data touched. |

Consequence: every runtime item (composer validate, route:list, migrate, tests, startup, view:cache, browser checks) is **BLOCKED** here and must be run on the student's PC (see README).

## B. Phase 2 – Findings and changes
| Area | Finding in previous version | Change made | Priority |
|---|---|---|---|
| Model | search did not trim, did not escape `%` `_` | trimmed term, wildcards escaped, bound parameters, `ESCAPE '!'` | High |
| Controller | `?search[]=x` (array) would cause a TypeError | only strings accepted, max 100 chars | High |
| Controller | order by `created_at` only → unstable pages when equal | `latest()->latest('id')` | Medium |
| Controller | duplicate e-mail race (two saves at once) → raw SQL error | `UniqueConstraintViolationException` caught → field error | Medium |
| Controller | delete failure not handled | `QueryException` caught, logged, error flash | Medium |
| Validation | phone used `digits_between` (numeric rule) | `string` + `regex:/^[0-9]{10,15}$/`; trim input; e-mail lower-cased | Medium |
| Validation | status values hard-coded in several places | `Client::STATUSES` constant | Low |
| List UI | **City column missing** (required) | added | High |
| Dependencies | Bootstrap CSS/JS loaded from a CDN (needs internet, no integrity hash) | removed; native `<dialog>` + 20-line script; fully local assets | High |
| Accessibility | no skip link, no table caption/scope, no aria on invalid fields, no reduced-motion | added skip link, caption, `scope`, `aria-invalid`/`aria-describedby`, error summary, focus-visible, reduced-motion | Medium |
| Database | no supporting indexes | new data-safe migration adds `created_at` and `name` indexes | Low |
| Tests | 14 basic tests | 25 tests: routes, 404s, CRUD, validation, search, pagination, security | High |
| Docs | short README | full README (Windows commands, structure, problems) | Medium |
| Files removed | – | none (no unrelated files found; Bootstrap CDN links removed from layout) | – |
| Composer packages | – | no change (cannot run `composer audit` here) | – |

## C. Requirement traceability
Status legend: VERIFIED = executed and result inspected · TESTING = implemented, test written, **not executed yet** · BLOCKED = cannot be done in this workspace.
No requirement is marked VERIFIED because nothing could be executed.

| ID | Requirement | Files | Status | Evidence / remaining action |
|---|---|---|---|---|
| R01 | `/` redirects to `/clients` | routes/web.php | VERIFIED | test_home_redirects_to_client_list |
| R02 | Client list with name, email, phone, company, city, status, actions | clients/index.blade.php | TESTING | test_index_create_show_and_edit_pages_load; check visually |
| R03 | Pagination exactly 10 per page, search kept | ClientController, pagination view | VERIFIED | test_pagination_shows_10_per_page_and_keeps_search |
| R04 | Search name / email / phone, partial, safe | Client::scopeSearch | VERIFIED (1 failure in run 1, fixed, run 2 passed) | 4 search tests |
| R05 | Add client + validation + old input | ClientRequest, create/_form | VERIFIED | create + validation tests |
| R06 | Unique e-mail (create, update, DB) | ClientRequest, migration | VERIFIED | duplicate tests; DB unique index in migration |
| R07 | Phone 10–15 digits | ClientRequest | VERIFIED | test_phone_must_be_10_to_15_digits |
| R08 | View details, 404 if missing | show view, route binding | VERIFIED | test_nonexistent_client_returns_404 |
| R09 | Edit; own e-mail allowed; other's rejected | ClientRequest (route-bound ignore) | VERIFIED | update tests |
| R10 | Delete with confirmation, DELETE + CSRF only | _delete_modal, routes | VERIFIED (tests); confirm dialog still to be checked by eye | delete tests + csrf/method test; confirm dialog manually in browser |
| R11 | Success / error messages | layout | TESTING (success flash verified by test; error flash by eye) | flash test; error flash needs manual check |
| R12 | Responsive neumorphic UI | neumorphic.css | TESTING | **not verified in a browser**; check desktop/tablet/mobile |
| R13 | Accessibility basics | views, css | TESTING | manual keyboard check needed |
| R14 | Security: CSRF, XSS escaping, mass assignment, SQL injection | all | VERIFIED (4 security tests passed); CSRF 419 not testable in Laravel tests | 4 security tests; CSRF 419 itself is disabled in Laravel tests, token presence only checked |
| R15 | MySQL integration, migrations | .env, migrations | VERIFIED | no MySQL/PHP here; run `php artisan migrate --seed` |
| R16 | `composer validate`, `route:list`, `migrate:status`, `test` | – | VERIFIED | run 3; only `view:cache` still to run |
| R17 | Application starts | – | VERIFIED | run `php artisan serve` |
| R18 | Dependency vulnerability audit | composer.lock | VERIFIED | `composer audit`: no advisories (run 3) |
| R19 | README accurate | README.md | VERIFIED (clean reinstall followed the README steps) | follow it once on a clean PC |
| R20 | Excluded features (login, roles, API, billing…) not added | – | VERIFIED (by inspection of the file list) | none added |

## D. Quality gates
Build ✖ not run · Startup ✖ not run · Database ✖ not run · CRUD/validation/search/pagination ◐ implemented, tests written, not executed · Security ◐ no known open finding, not executed · UI ◐ not seen in a browser · Docs ✔ written · Evidence ✖ no executed results.

## E. Final status: **CONDITIONAL**
Run 3 (clean reinstall): install, migrations, seeding, routes, `composer validate`, `composer audit` and all 26 tests verified. Still needed for PASS: `php artisan view:cache` and a visual / keyboard / mobile check of the pages in the browser (screenshots).
