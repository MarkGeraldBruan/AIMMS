# Copilot Instructions for AGRISUPPLY

## Project Overview
- **AGRISUPPLY** is a Laravel-based supply management system for agricultural organizations.
- Main features: supply inventory, user management (admin/user roles), reporting (PDF/Excel), and help requests.
- Data is stored in MySQL/MariaDB; UI uses Blade templates and Bootstrap/Tailwind CSS.

## Architecture & Key Patterns
- **MVC Structure:**
  - `app/Models/` — Eloquent models (e.g., `Supplies.php`, `User.php`).
  - `app/Http/Controllers/` — Controllers for web/API logic. Example: `Client/RpciController.php` handles supply reporting and exports.
  - `resources/views/` — Blade templates for UI.
- **Exports/Reports:**
  - PDF: Uses `Barryvdh\DomPDF` (`exportPDF` methods, e.g., in `RpciController`).
  - Excel: Uses `Maatwebsite\Excel` (`exportExcel` methods, e.g., in `RpciController`).
  - Export logic is in `app/Exports/` (e.g., `RpciExport.php`).
- **Filtering & Querying:**
  - Controllers use request filters (date, department, status) to build queries (see `RpciController@index`).
  - Data mapping for reports uses Laravel collections (`map`).
- **User Roles:**
  - Role logic is enforced in controllers and middleware.
  - Admins manage users/supplies; users have limited access.

## Developer Workflows
- **Install:** `composer install` and `npm install` (see README).
- **Environment:** Copy `.env.example` to `.env` and configure DB.
- **Migrate:** `php artisan migrate` to set up schema.
- **Serve:** `php artisan serve` for local dev server.
- **Testing:** PHPUnit config in `phpunit.xml`; tests in `tests/Feature` and `tests/Unit`.
- **Export/Reporting:**
  - PDF: Triggered via controller methods, uses Blade views for layout.
  - Excel: Uses export classes in `app/Exports/`.

## Conventions & Patterns
- **Naming:**
  - Models: Singular (`Supplies`, `User`).
  - Controllers: Plural, grouped by domain (`Client`, `Admin`).
- **Blade Views:**
  - Located in `resources/views/client/report/` for reporting UIs.
- **External Packages:**
  - `barryvdh/laravel-dompdf` for PDF.
  - `maatwebsite/excel` for Excel.
- **Data Flow:**
  - Controllers fetch and filter data, pass to Blade views or export classes.
  - Exports use mapped data objects for report rows.

## Key Files & Directories
- `app/Http/Controllers/Client/RpciController.php` — Example of filtering, reporting, and export logic.
- `app/Exports/` — Export classes for Excel/PDF.
- `resources/views/client/report/rpci/` — Blade templates for RPCI reports.
- `database/migrations/` — Schema definitions.
- `README.md` — Setup and workflow reference.

## Example: Filtering Supplies for Reports
```php
// In RpciController@index
$query = Supplies::query();
if ($request->filled('date_from')) {
    $query->whereDate('created_at', '>=', $request->date_from);
}
// ...other filters...
$supplies = $query->orderBy('created_at', 'desc')->get();
```

## Tips for AI Agents
- Always check for request filters in controllers when generating report logic.
- Use Eloquent for data access; avoid raw SQL unless necessary.
- Reference export classes for Excel logic; PDF uses Blade views.
- Follow existing naming and directory conventions for new features.

---

_If any section is unclear or missing, please provide feedback for further refinement._
