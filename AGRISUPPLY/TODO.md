# Fix IIRUP Export PDF and Excel

## Tasks
- [x] Rename class in `app/exports/IirupExport.php` from `PpesExport` to `IirupExport`
- [x] Update `PpesController::exportExcel` to use `new IirupExport($request)`
- [x] Update `resources/views/client/report/iirup/index.blade.php` to use IIRUP routes for reset and export links
- [x] Test PDF and Excel exports for IIRUP
