<?php

use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\SuppliesController;
use App\Http\Controllers\Client\DeletedSupplyController;
use App\Http\Controllers\Client\DeletedEquipmentController;
use App\Http\Controllers\Client\EquipmentController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ReportController;
use App\Http\Controllers\Client\StockCardController;
use App\Http\Controllers\Client\PropertyCardController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\AnnouncementController;
use App\Http\Controllers\Client\HelpController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Client\RsmiController;
use App\Http\Controllers\Client\RpciController;
use App\Http\Controllers\Client\RpcPpeController;
use App\Http\Controllers\Client\PpesController;
use App\Http\Controllers\Client\IirupController;
use App\Http\Controllers\Client\AboutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserCredentialMail;

// Root route - redirect to dashboard after login
Route::get('/', function () {
    return auth()->check() ? redirect('/client/dashboard') : redirect('/home');
});

// Custom Forgot Password Routes (MUST BE BEFORE Auth::routes())
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetCode'])->name('password.email');

// Verify Code Routes
Route::get('/verify-code', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/verify-code', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::post('/resend-code', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resendCode'])->name('password.resend');

// Reset Password Routes
Route::get('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Authentication routes - only login and register (NO password reset)
Auth::routes(['register' => true, 'reset' => false, 'verify' => false, 'confirm' => false]);

// Home route (landing page before login)
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ============================================
// EMAIL TESTING ROUTES - TEMPORARY
// Remove these after testing is complete
// ============================================

// 1. PREVIEW EMAIL (No login required, just to see the template)
Route::get('/preview-user-email', function () {
    $testUser = new User();
    $testUser->name = 'Juan Dela Cruz';
    $testUser->email = 'juan.delacruz@example.com';
    $testUser->role = 'user';
    
    $plainPassword = 'TempPassword123!';
    
    return view('emails.user-credentials', [
        'user' => $testUser,
        'plainPassword' => $plainPassword
    ]);
});

// 2. CHECK EMAIL CONFIGURATION (Requires login)
Route::get('/check-email-config', function () {
    $config = [
        'MAIL_MAILER' => config('mail.default'),
        'MAIL_HOST' => config('mail.mailers.smtp.host'),
        'MAIL_PORT' => config('mail.mailers.smtp.port'),
        'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
        'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '✅ SET' : '❌ NOT SET',
        'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
        'MAIL_FROM_ADDRESS' => config('mail.from.address'),
        'MAIL_FROM_NAME' => config('mail.from.name'),
    ];
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Email Configuration Check</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #296218; border-bottom: 3px solid #296218; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            td { padding: 12px; border: 1px solid #ddd; }
            td:first-child { background: #f8f9fa; font-weight: bold; width: 40%; }
            .buttons { margin-top: 30px; display: flex; gap: 15px; }
            .btn { padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
            .btn-primary { background: #296218; color: white; }
            .btn-secondary { background: #6c757d; color: white; }
            .btn:hover { opacity: 0.8; }
            .status { padding: 5px 10px; border-radius: 3px; font-size: 12px; }
            .status.success { background: #d4edda; color: #155724; }
            .status.error { background: #f8d7da; color: #721c24; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📧 Email Configuration Check</h1>
            <table>';
    
    foreach ($config as $key => $value) {
        $statusClass = ($key === 'MAIL_PASSWORD' && strpos($value, '✅') !== false) ? 'success' : '';
        $html .= "<tr><td>$key</td><td><span class='status $statusClass'>$value</span></td></tr>";
    }
    
    $html .= '</table>
            <div class="buttons">
                <a href="/send-test-email" class="btn btn-primary">📤 Send Test Email</a>
                <a href="/preview-user-email" class="btn btn-secondary">👁️ Preview Email</a>
                <a href="/check-email-log" class="btn btn-secondary">📄 Check Log File</a>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
})->middleware('auth');

// 3. SEND TEST EMAIL TO YOURSELF (Requires login)
Route::get('/send-test-email', function () {
    try {
        $testUser = new User();
        $testUser->name = 'Test User';
        $testUser->email = 'bolinasrb.381.stud@cdd.edu.ph'; // YOUR EMAIL
        $testUser->role = 'user';
        
        $plainPassword = 'TestPassword123!';
        
        Mail::to($testUser->email)->send(new UserCredentialMail($testUser, $plainPassword));
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Test Result</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
                h1 { color: #28a745; }
                .icon { font-size: 60px; margin-bottom: 20px; }
                .email { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; font-family: monospace; }
                .buttons { margin-top: 30px; display: flex; gap: 15px; justify-content: center; }
                .btn { padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
                .btn-primary { background: #296218; color: white; }
                .btn:hover { opacity: 0.8; }
                ul { text-align: left; margin: 20px auto; max-width: 400px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="icon">✅</div>
                <h1>Email Sent Successfully!</h1>
                <p>A test email has been sent to:</p>
                <div class="email">' . $testUser->email . '</div>
                <h3>Next Steps:</h3>
                <ul>
                    <li>Check your inbox</li>
                    <li>Check spam/junk folder</li>
                    <li>Wait 2-5 minutes if not received</li>
                </ul>
                <div class="buttons">
                    <a href="/preview-user-email" class="btn btn-primary">Preview Email Template</a>
                    <a href="/check-email-config" class="btn btn-primary">Check Config</a>
                </div>
            </div>
        </body>
        </html>';
                
    } catch (\Exception $e) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Test Error</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
                .container { max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #dc3545; }
                .icon { font-size: 60px; margin-bottom: 20px; text-align: center; }
                .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #dc3545; }
                .solutions { background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .solutions h3 { color: #856404; margin-top: 0; }
                ul { margin: 10px 0; padding-left: 20px; }
                .buttons { margin-top: 30px; display: flex; gap: 15px; }
                .btn { padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
                .btn-primary { background: #296218; color: white; }
                .btn:hover { opacity: 0.8; }
                code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="icon">❌</div>
                <h1>Error Sending Email</h1>
                <div class="error">
                    <strong>Error Message:</strong><br>
                    ' . htmlspecialchars($e->getMessage()) . '
                </div>
                
                <div class="solutions">
                    <h3>🔧 Common Solutions:</h3>
                    <ul>
                        <li>Check if <code>MAIL_PASSWORD</code> is set correctly in .env</li>
                        <li>Make sure you used Gmail <strong>App Password</strong> (not regular password)</li>
                        <li>Run: <code>php artisan config:clear</code></li>
                        <li>Check if Gmail 2FA is enabled</li>
                        <li>Verify your email: bolinasrb.381.stud@cdd.edu.ph</li>
                        <li>Try using <code>MAIL_MAILER=log</code> for testing</li>
                    </ul>
                </div>
                
                <div class="buttons">
                    <a href="/check-email-config" class="btn btn-primary">Check Configuration</a>
                    <a href="/preview-user-email" class="btn btn-primary">Preview Email</a>
                </div>
            </div>
        </body>
        </html>';
    }
})->middleware('auth');

// 4. CHECK EMAIL LOG (if using MAIL_MAILER=log)
Route::get('/check-email-log', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (!file_exists($logFile)) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Email Log</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
                .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
                h1 { color: #6c757d; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>❌ Log file not found</h1>
                <p>No emails have been logged yet.</p>
                <p>Try creating a user or sending a test email first.</p>
            </div>
        </body>
        </html>';
    }
    
    // Get last 150 lines
    $lines = file($logFile);
    $recentLines = array_slice($lines, -150);
    $content = implode('', $recentLines);
    
    // Highlight email-related content
    $content = htmlspecialchars($content);
    $content = str_replace('Content-Type: text/html', '<span style="background: yellow; font-weight: bold;">Content-Type: text/html</span>', $content);
    $content = str_replace('Subject:', '<span style="background: #d4edda; font-weight: bold;">Subject:</span>', $content);
    $content = str_replace('To:', '<span style="background: #cce5ff; font-weight: bold;">To:</span>', $content);
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Email Log</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #296218; border-bottom: 3px solid #296218; padding-bottom: 10px; }
            .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
            pre { background: #f8f9fa; padding: 20px; overflow-x: auto; border-radius: 5px; border: 1px solid #ddd; font-size: 12px; line-height: 1.5; }
            .buttons { margin: 20px 0; display: flex; gap: 15px; }
            .btn { padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; background: #296218; color: white; }
            .btn:hover { opacity: 0.8; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📄 Recent Email Log</h1>
            <div class="info">
                <strong>ℹ️ Note:</strong> Showing last 150 lines from laravel.log<br>
                Location: storage/logs/laravel.log
            </div>
            <div class="buttons">
                <a href="/preview-user-email" class="btn">Preview Email Template</a>
                <a href="/check-email-config" class="btn">Check Config</a>
            </div>
            <pre>' . $content . '</pre>
        </div>
    </body>
    </html>';
})->middleware('auth');

// ============================================
// END OF EMAIL TESTING ROUTES
// ============================================

// Protected client routes
Route::prefix('client')->middleware('auth:web')->group(function(){
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('client.dashboard.stats');
    Route::get('/dashboard/recent-items', [DashboardController::class, 'getRecentItems'])->name('client.dashboard.recent');
    Route::get('/dashboard/low-stock', [DashboardController::class, 'getLowStockItems'])->name('client.dashboard.lowstock');
    
    // User management routes
    Route::resource('users', UserController::class);
    
    // Profile settings routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('client.profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('client.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('client.profile.password');
    Route::delete('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('client.profile.remove-avatar');
    Route::delete('/profile/remove-logo', [ProfileController::class, 'removeLogo'])->name('client.profile.remove-logo');
    
    // Supplies routes with additional functionality
    Route::resource('supplies', SuppliesController::class);
    Route::get('supplies-export', [SuppliesController::class, 'export'])->name('supplies.export');

    Route::prefix('deleted-supplies')->name('deleted-supplies.')->group(function () {
        Route::get('/', [DeletedSupplyController::class, 'index'])->name('index');
        Route::get('/{id}', [DeletedSupplyController::class, 'show'])->name('show');
        Route::post('/{id}/restore', [DeletedSupplyController::class, 'restore'])->name('restore');
        Route::delete('/{id}/permanent', [DeletedSupplyController::class, 'permanentDelete'])->name('permanent-delete');
    });

    // Equipment routes with export functionality
    Route::get('equipment/api/classifications', [EquipmentController::class, 'getClassifications'])->name('equipment.classifications');
    Route::resource('equipment', EquipmentController::class)->names([
        'index' => 'client.equipment.index'
    ]);
    Route::get('equipment-export', [EquipmentController::class, 'export'])->name('equipment.export');
    Route::prefix('deleted-equipment')->name('deleted-equipment.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Client\DeletedEquipmentController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Client\DeletedEquipmentController::class, 'show'])->name('show');
        Route::post('/{id}/restore', [\App\Http\Controllers\Client\DeletedEquipmentController::class, 'restore'])->name('restore');
        Route::delete('/{id}/permanent', [\App\Http\Controllers\Client\DeletedEquipmentController::class, 'permanentDelete'])->name('permanent-delete');
    });

    // Report routes
    Route::resource('reports', ReportController::class)->names([
        'index' => 'client.reports.index'
    ]);
        
    // Individual report routes
    Route::get('report/rsmi', [ReportController::class, 'rsmi'])->name('client.report.rsmi');
    Route::get('report/rpci', [ReportController::class, 'rpci'])->name('client.report.rpci');
    Route::get('report/rpc-ppe', [RpcPpeController::class, 'index'])->name('client.report.rpc-ppe');

    // Stock Card routes (Fixed naming convention)
    Route::prefix('stockcard')->name('client.stockcard.')->group(function () {
        Route::get('/', [StockCardController::class, 'index'])->name('index');
        Route::get('/show/{id}', [StockCardController::class, 'show'])->name('show');
        Route::get('/export/excel/{id}', [StockCardController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/all/excel', [StockCardController::class, 'exportAllStockCards'])->name('export.all.excel');

        // Stock In routes
        Route::get('/stock-in', [StockCardController::class, 'stockIn'])->name('stock-in');
        Route::post('/stock-in', [StockCardController::class, 'processStockIn'])->name('stock-in.process');

        // Stock Out routes
        Route::get('/stock-out', [StockCardController::class, 'stockOut'])->name('stock-out');
        Route::post('/stock-out', [StockCardController::class, 'processStockOut'])->name('stock-out.process');

        // Audit Trail route 
        Route::get('/audit-trail', [StockCardController::class, 'auditTrail'])->name('audit-trail');
    });

    Route::resource('propertycard', PropertyCardController::class)->names([
        'index' => 'client.propertycard.index',
        'show' => 'client.propertycard.show'
    ]);
    Route::get('propertycard/export/excel/{id}', [PropertyCardController::class, 'exportExcel'])->name('client.propertycard.export.excel');
    
    
    Route::post('announcement/{id}/reserve', [AnnouncementController::class, 'reserveSupplies'])
        ->name('client.announcement.reserve');

    Route::post('announcement/{id}/stock-out', [AnnouncementController::class, 'stockOutSupplies'])
        ->name('client.announcement.stock-out');

    Route::post('announcement/bulk-publish', [AnnouncementController::class, 'bulkPublish'])
        ->name('client.announcement.bulk-publish');

    Route::post('announcement/bulk-delete', [AnnouncementController::class, 'bulkDelete'])
        ->name('client.announcement.bulk-delete');

    Route::post('announcement/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
        ->name('client.announcement.toggle-status');

    Route::resource('announcement', AnnouncementController::class)->names([
        'index' => 'client.announcement.index',
        'create' => 'client.announcement.create',
        'store' => 'client.announcement.store',
        'show' => 'client.announcement.show',
        'edit' => 'client.announcement.edit',
        'update' => 'client.announcement.update',
        'destroy' => 'client.announcement.destroy'
    ]);
    
    // Help routes 
    Route::resource('help', HelpController::class)->names([
        'index' => 'client.help.index',
        'create' => 'client.help.create',
        'store' => 'client.help.store',
        'show' => 'client.help.show',
        'edit' => 'client.help.edit',
        'update' => 'client.help.update',
        'destroy' => 'client.help.destroy'
    ]);
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent']);

    // About routes
    Route::get('/about', [AboutController::class, 'index'])->name('client.about.index');

    // Alternative route names for backward compatibility
    Route::get('inventory', [SuppliesController::class, 'index'])->name('client.inventory');
    Route::get('inventory/{supply}', [SuppliesController::class, 'show'])->name('client.inventory.show');
});

// Route para sa RSMI report page
Route::prefix('client/report')->group(function () {
    Route::get('/rsmi', [RsmiController::class, 'index'])->name('client.report.rsmi');
    Route::get('/rsmi/export/pdf', [RsmiController::class, 'exportPDF'])->name('client.report.rsmi.export.pdf');
    Route::get('/rsmi/export/excel', [RsmiController::class, 'exportExcel'])->name('client.report.rsmi.export.excel');

    // RPCI report routes
    Route::get('/rpci', [RpciController::class, 'index'])->name('client.report.rpci');
    // Use ReportController export handlers for RPCI quick-export endpoints
    Route::get('/rpci/export/pdf', [App\Http\Controllers\Client\ReportController::class, 'exportRpciPdf'])->name('client.report.rpci.export.pdf');
    Route::get('/rpci/export/excel', [App\Http\Controllers\Client\ReportController::class, 'exportRpciExcel'])->name('client.report.rpci.export.excel');

    // PPES report routes
    Route::get('/ppes', [PpesController::class, 'index'])->name('client.report.ppes');
    Route::get('/ppes/export/pdf', [PpesController::class, 'exportPDF'])->name('client.report.ppes.export.pdf');
    Route::get('/ppes/export/excel', [PpesController::class, 'exportExcel'])->name('client.report.ppes.export.excel');

    // RPC-PPE report routes
    Route::get('/rpc-ppe', [RpcPpeController::class, 'index'])->name('client.report.rpc-ppe');
    Route::get('/rpc-ppe/export/pdf', [RpcPpeController::class, 'exportPDF'])->name('client.report.rpc-ppe.export.pdf');
    Route::get('/rpc-ppe/export/excel', [RpcPpeController::class, 'exportExcel'])->name('client.report.rpc-ppe.export.excel');

    // IIRUP report routes
    Route::get('/iirup', [IirupController::class, 'index'])->name('client.report.iirup');
    Route::get('/iirup/export/pdf', [IirupController::class, 'exportPDF'])->name('client.report.iirup.export.pdf');
    Route::get('/iirup/export/excel', [IirupController::class, 'exportExcel'])->name('client.report.iirup.export.excel');

    // RPC Semi-Expendable Properties (High Value) report routes
    Route::get('/rpc-semi-high', [App\Http\Controllers\Client\RpcSemiHighController::class, 'index'])->name('client.report.rpc-semi-high');
    Route::get('/rpc-semi-high/export/pdf', [App\Http\Controllers\Client\RpcSemiHighController::class, 'exportPDF'])->name('client.report.rpc-semi-high.export.pdf');
    Route::get('/rpc-semi-high/export/excel', [App\Http\Controllers\Client\RpcSemiHighController::class, 'exportExcel'])->name('client.report.rpc-semi-high.export.excel');

    // RPC Semi-Expendable Properties (Low Value) report routes
    Route::get('/rpc-semi-low', [App\Http\Controllers\Client\RpcSemiLowController::class, 'index'])->name('client.report.rpc-semi-low');
    Route::get('/rpc-semi-low/export/pdf', [App\Http\Controllers\Client\RpcSemiLowController::class, 'exportPDF'])->name('client.report.rpc-semi-low.export.pdf');
    Route::get('/rpc-semi-low/export/excel', [App\Http\Controllers\Client\RpcSemiLowController::class, 'exportExcel'])->name('client.report.rpc-semi-low.export.excel');
});

// Fallback route for authenticated users
Route::middleware('auth:web')->group(function(){
    // Redirect authenticated users to dashboard if they hit /home
    Route::get('/home', function() {
        return redirect('/client/dashboard');
    });
});